<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CustomerResource;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    // method GET
    public function index()
    {
        $customers = Customer::get();
        if ($customers->count() > 0) {
            return response()->json([
                'message' => 'Get customer success',
                'data' => CustomerResource::collection($customers)
            ], 200);
        } else {
            return response()->json(['message' => 'No record available'], 200);
        }
    }

    // method POST
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_name' => [
                'required',
                'string',
                'max:255',
                'unique:customers,user_name',
                'regex:/^[a-zA-Z0-9_]+$/',
            ],
            'full_name' => 'required|string|max:255',
            'gender' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => ['required', 'string', 'email', 'regex:/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\\.,;:\s@\"]+\.)+[^<>()[\]\\.,;:\s@\"]{2,})$/i', 'unique:customers,email'],
            'phone_number' => ['required', 'string', 'regex:/^[0-9]{10}$/', 'unique:customers,phone_number'],
            'password' => 'required|string|min:8',
            'address' => 'required|string|max:255',
        ]);

        $date_of_birth = null;
        if ($request->date_of_birth) {
            if (Carbon::hasFormat($request->date_of_birth, 'd/m/Y')) {
                $date_of_birth = Carbon::createFromFormat('d/m/Y', $request->date_of_birth)->format('Y-m-d');
            } elseif (Carbon::hasFormat($request->date_of_birth, 'd-m-Y')) {
                $date_of_birth = Carbon::createFromFormat('d-m-Y', $request->date_of_birth)->format('Y-m-d');
            } elseif (Carbon::hasFormat($request->date_of_birth, 'Y/m/d')) {
                $date_of_birth = Carbon::createFromFormat('Y/m/d', $request->date_of_birth)->format('Y-m-d');
            } elseif (Carbon::hasFormat($request->date_of_birth, 'Y-m-d')) {
                $date_of_birth = Carbon::createFromFormat('Y-m-d', $request->date_of_birth)->format('Y-m-d');
            } else {
                return response()->json([
                    'message' => 'Invalid date format. Accepted formats are d/m/Y, d-m-Y, Y/m/d, Y-m-d.',
                ], 422);
            }
        }

        if ($validator->fails()) {
            Log::error('Validation failed', [
                'errors' => $validator->messages(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Field is empty or invalid',
                'error' => $validator->messages(),
            ], 422);
        }

        $hashed_password = Hash::make($request->password);

        // Handle image with Storage
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('customers', $imageName, 'public');
            $imageUrl = Storage::url($imagePath);
        }

        $customers = Customer::create([
            'user_name' => $request->user_name,
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'date_of_birth' => $date_of_birth,
            'image' => $imageUrl ?? null,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => $hashed_password,
            'address' => $request->address,
        ]);

        return response()->json([
            'message' => 'Customer created success',
            'data' => new CustomerResource($customers)
        ], 201);
    }

    // method GET Detail with customer_id
    public function show($customer_id)
    {
        try {
            $customer = Customer::where('customer_id', $customer_id)->first();
            if (!$customer) {
                return response()->json([
                    'message' => 'Customer not found',
                    'customer_id' => $customer_id
                ], 404);
            }

            return response()->json([
                'message' => 'Get customer success with customer_id',
                'data' => new CustomerResource($customer)
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to get customer information', [
                'error' => $e->getMessage(),
                'customer_id' => $customer_id
            ]);

            return response()->json([
                'message' => 'Failed to get customer information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // method PUT
    public function update(Request $request, Customer $customer)
    {

        $validator = Validator::make($request->all(), [
            'user_name' => [
                'sometimes',
                'string',
                'max:255',
                'unique:customers,user_name',
                'regex:/^[a-zA-Z0-9_]+$/',
            ],
            'full_name' => 'sometimes|string|max:255',
            'gender' => 'sometimes|string|max:255',
            'date_of_birth' => 'sometimes|date',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => ['sometimes', 'string', 'email', 'regex:/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\\.,;:\s@\"]+\.)+[^<>()[\]\\.,;:\s@\"]{2,})$/i', 'unique:customers,email'],
            'phone_number' => ['sometimes', 'string', 'regex:/^[0-9]{10}$/', 'unique:customers,phone_number'],
            'password' => 'sometimes|string|min:8',
            'address' => 'sometimes|string|max:255',
        ]);

        if ($request->date_of_birth) {
            if (Carbon::hasFormat($request->date_of_birth, 'd/m/Y')) {
                $date_of_birth = Carbon::createFromFormat('d/m/Y', $request->date_of_birth)->format('Y-m-d');
            } elseif (Carbon::hasFormat($request->date_of_birth, 'd-m-Y')) {
                $date_of_birth = Carbon::createFromFormat('d-m-Y', $request->date_of_birth)->format('Y-m-d');
            } elseif (Carbon::hasFormat($request->date_of_birth, 'Y/m/d')) {
                $date_of_birth = Carbon::createFromFormat('Y/m/d', $request->date_of_birth)->format('Y-m-d');
            } elseif (Carbon::hasFormat($request->date_of_birth, 'Y-m-d')) {
                $date_of_birth = Carbon::createFromFormat('Y-m-d', $request->date_of_birth)->format('Y-m-d');
            }
        }

        if ($validator->fails()) {
            Log::error('Validation failed', [
                'errors' => $validator->messages(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Field is empty or invalid',
                'error' => $validator->messages(),
            ], 422);
        }

        $hashed_password = Hash::make($request->password);

        // Handle image with Storage
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('customers', $imageName, 'public');
            $imageUrl = Storage::url($imagePath);
        }

        $customer->update([
            'user_name' => $request->user_name ?? $customer->user_name,
            'full_name' => $request->full_name ?? $customer->full_name,
            'gender' => $request->gender ?? $customer->gender,
            'date_of_birth' => $date_of_birth ?? $customer->date_of_birth,
            'image' => $imageUrl ?? $customer->image,
            'email' => $request->email ?? $customer->email,
            'phone_number' => $request->phone_number ?? $customer->phone_number,
            'password' => $hashed_password ?? $customer->password,
            'address' => $request->address ?? $customer->address,
        ]);

        return response()->json([
            'message' => 'Customer updated success',
            'data' => new CustomerResource($customer)
        ], 200);
    }

    // method DELETE
    public function destroy(Customer $customer)
    {
        if ($customer->image) {
            $imagePaths = json_decode($customer->image);
            foreach ($imagePaths as $imagePath) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $imagePath));
            }
        }

        $customer->delete();

        return response()->json([
            'message' => 'Customer deleted successfully',
        ], 200);
    }

    // method handle login customer with email, username, phone number, and password
    public function authenticateLoginCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'identifier.required' => 'The identifier field is required.',
            'password.required' => 'The password field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $customer = Customer::where('email', $request->identifier)
                            ->orWhere('phone_number', $request->identifier)
                            ->orWhere('user_name', $request->identifier)
                            ->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'message' => 'Incorrect username and password',
                'error_code' => 'AUTH_FAILED',
            ], 401);
        }

        $customer->last_login = now();
        $customer->save();

        $accessToken = $customer->createToken('customer-access-token')->plainTextToken;

        $refreshToken = Str::random(64);
        $expiresAt = now()->addDays(30);

        DB::table('refresh_tokens')->insert([
            'customer_id' => $customer->customer_id,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'id' => $customer->customer_id,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_in' => 86400, //1 day = 86400 second
            ],
        ], 200);
    }

    // method refreshAccessToken Customer
    public function refreshAccessToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string|exists:refresh_tokens,refresh_token',
        ], [
            'refresh_token.required' => 'The refresh token field is required.',
            'refresh_token.exists' => 'The provided refresh token is invalid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $refreshToken = $request->refresh_token;
        $tokenRecord = DB::table('refresh_tokens')->where('refresh_token', $refreshToken)->first();

        if (!$tokenRecord) {
            return response()->json([
                'message' => 'Invalid refresh token.',
            ], 401);
        }

        if (Carbon::now()->greaterThan(Carbon::parse($tokenRecord->expires_at))) {
            DB::table('refresh_tokens')->where('refresh_token', $refreshToken)->delete();
            return response()->json([
                'message' => 'Refresh token has expired.',
            ], 401);
        }

        $customer = Customer::where('customer_id', $tokenRecord->customer_id)->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found.',
            ], 404);
        }

        $newAccessToken = $customer->createToken('customer-access-token')->plainTextToken;

        return response()->json([
            'message' => 'Access token refreshed successfully.',
            'access_token' => $newAccessToken,
        ], 200);
    }

}
