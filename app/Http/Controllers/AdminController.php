<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\business_details;
use DB;
use App\Models\ivr_configurations;
use Carbon\Carbon;
use App\Models\CallRecording;


class AdminController extends Controller
{
    public function index()
    {
        return view('Admin.dashbaord.index');
    }
    public function Accounts()
    {
        $users = User::with('business_details')->where('role_id', 3)->get();
     
        return view('Admin.Accounts.index',compact('users'));
    }

    public function store(Request $request)
    {
       
       try {

        DB::beginTransaction();
       
        $request->validate([
            'business_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'did_number' => 'required|string|max:20',
            'contact' => 'required|string|max:20',
            'business_phone' => 'required|string|max:20',
            'call_person_name' => 'nullable|string|max:255',
            'seconds_past' => 'nullable|integer|min:0',
            'timezone' => 'nullable|string|max:255|not_in:Select Timezone',
        ]);
        
          
           $user = User::create([
               'name' => $request->name,
               'email' => $request->email,
               'password' =>  bcrypt($request->password),
               'role_id' =>  3,
           ]);
           if ($user) {
            
                $accountData = [
                    'user_id' => $user->id,
                    'business_name' => $request->business_name,
                    'designation' => $request->designation,
                    'address' => $request->address,
                    'contact' => $request->contact,
                    'agent_greeting' => $request->agent_greeting,
                    'website' => $request->website,
                    'obituary_link' => $request->obituary_link,
                    'directions_link' => $request->directions_link,
                    'agent_notes' => $request->agent_notes,
                    'fax_numbers' => $request->fax_numbers,
                    'bulk_emails' => $request->bulk_emails,
                    'bulk_sms' => $request->bulk_sms,
                    'business_phone' => $request->business_phone,
                    'did_config' => $request->has('did_config') ? true : false,
                    'did_number' => $request->did_number,
                    'callback_required' => $request->has('callback_required') ? true : false,
                    'call_person_name' => $request->call_person_name,
                    'seconds_past' => $request->second_past,
                    'send_reminder_tx'=>$request->reminder,
                    'active_agents'=>$request->active_agents,
                    'notifications'=>$request->notifications,
                    'survey'=>$request->survey,
                    'join_calls'=>$request->join_calls,
                    'schedule_greeting'=>$request->schedule_greeting,
                    'no_of_oncall' => $request->no_of_oncall,
                    'timezone' => $request->timezone !== 'Select Timezone' ? $request->timezone : null,
                ];
            

                $account = business_details::create($accountData);
                
                if($request->did_number || $request->business_phone)
                {
                    $ivr = ivr_configurations::create([
                        'did_number' => $request->did_number,
                        'business_phone' => $request->business_phone,
                        'added_by' => $user->id,
                        'ivr_type' => "TTS",
                        'ttstype' => 'john',
                        'repeat_count' => 1,
                    ]);
                }

                DB::commit();
              
           } 
           return redirect()->route('Admin.index')->with('success', 'User created successfully.');
       } catch (Exception $e) {
           DB::rollBack();
           return back()->withErrors([
               'error' => 'There was an error processing your request. Please try again later.',
           ]);
       }
      
    }

    public function show($id)
    {
        $user = User::with('business_details')->findOrFail($id);
        return response()->json([
            'user' => $user,
            'business_details' => $user->business_details
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'business_name' => 'required|string|max:255',
                'designation' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,'.$id,
                'password' => 'nullable|string|min:8',
                'did_number' => 'required|string|max:20',
                'contact' => 'required|string|max:20',
                'business_phone' => 'required|string|max:20',
                'call_person_name' => 'nullable|string|max:255',
                'second_past' => 'nullable|integer|min:0',
                'timezone' => 'nullable|string|max:255|not_in:Select Timezone',
            ]);

            // Find the user
            $user = User::findOrFail($id);

            // Prepare user data
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'contact' => $request->contact,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }

            // Update user
            $user->update($userData);

            // Prepare business details data
            $accountData = [
                'user_id' => $user->id,
                'business_name' => $request->business_name,
                'designation' => $request->designation,
                'address' => $request->address,
                'contact' => $request->contact,
                'agent_greeting' => $request->agent_greeting,
                'website' => $request->website,
                'obituary_link' => $request->obituary_link,
                'directions_link' => $request->directions_link,
                'agent_notes' => $request->agent_notes,
                'fax_numbers' => $request->fax_numbers,
                'bulk_emails' => $request->bulk_emails,
                'bulk_sms' => $request->bulk_sms,
                'business_phone' => $request->business_phone,
                'did_config' => $request->has('did_config') ? true : false,
                'did_number' => $request->did_number,
                'callback_required' => $request->has('callback_required') ? true : false,
                'call_person_name' => $request->call_person_name,
                'seconds_past' => $request->second_past,
                'send_reminder_tx'=>$request->reminder,
                'active_agents'=>$request->active_agents,
                'notifications'=>$request->notifications,
                'survey'=>$request->survey,
                'join_calls'=>$request->join_calls,
                'schedule_greeting'=>$request->schedule_greeting,
                'timezone' => $request->timezone !== 'Select Timezone' ? $request->timezone : null,
                'no_of_oncall' => $request->no_of_oncall,
            ];
        

            // Update or create business details
            if ($user->business_details) {
                $user->business_details()->update($accountData);
            } else {
                $accountData['user_id'] = $user->id;
                business_details::create($accountData);
            }

            DB::commit();

            return response()->json([
                'message' => 'User updated successfully'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'There was an error processing your request. Please try again later.'
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        // Password reset logic here
    }

    public function destroy(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $user = User::findOrFail($request->id);
            
            
            if ($user->business_details) {
                $user->business_details()->delete();
            }
         
            $user->delete();
            
            DB::commit();
            
            return response()->json([
                'message' => 'User deleted successfully'
            ]);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'There was an error deleting the user. Please try again later.'
            ], 500);
        }
    }

    public function updateUserStatus(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user) {
            $user->is_active = $request->is_active;
            $user->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
    
     public function monthly_report(Request $request)
    {

            $business = business_details::All();

        $from = $request->start_date;
        $to = $request->end_date;
        $filterName = $request->input('name');

        $data = collect(); // default empty collection

        if ($from && $to) {
            $data = CallRecording::join('users', 'call_recordings.user_id', '=', 'users.id')
            ->join('accounts', 'accounts.user_id', '=', 'users.id')
             ->selectRaw('
                    accounts.business_name as name,
                    DATE(call_recordings.created_at) as full_date,
                    COUNT(*) as voice
                ')
                ->when($filterName, function ($q) use ($filterName) {
                    $q->where('call_recordings.from_number', $filterName); // adjust if needed
                })
                ->whereBetween(DB::raw('DATE(call_recordings.created_at)'), [
                    Carbon::parse($from)->toDateString(),
                    Carbon::parse($to)->toDateString()
                ])
                ->groupBy(DB::raw('DATE(call_recordings.created_at)'), 'accounts.business_name')
                ->orderBy(DB::raw('DATE(call_recordings.created_at)'))
                ->get()
                ->map(function ($item) {
                    $carbonDate = Carbon::parse($item->full_date);
                    $item->date = $carbonDate->day;
                    $item->month = $carbonDate->month;
                    $item->year = $carbonDate->year;
                    $item->callback = 0;
                    $item->sms = 0;
                    return $item;
                });
        }

        return view('Admin.Reports.mothly-report', compact('data','business'));
    }

    


   
}
