<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;


class PlanController extends Controller
{
    public function AllPlans(): View
    {
        // Récupère tous les plans actifs, triés par ordre
        $plans = Plan::active()
                    ->orderBy('sort_order')
                    ->get();

        // Calcul des économies pour l'abonnement annuel (20% de réduction)
        $plans->each(function ($plan) {
            if ($plan->price > 0) {
                $plan->yearly_price = $plan->price * 12 * 0.8; // 20% de réduction
                $plan->yearly_saving = $plan->price * 12 * 0.2; // Économies
            }
        });

        return view('admin.backend.plan.all_plan', compact('plans'));
    }



    public function AddPlans(): View
    {
        return view('admin.backend.plan.add_plan');
    }
    // End Method

    public function StorePlans(Request $request)
    {
        Plan::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
            'currency' => $request->currency,
            'analyses_per_month' => $request->analyses_per_month,
            'projects_limit' => $request->projects_limit,
            'team_members_limit' => $request->team_members_limit,
            'api_calls_per_month' => $request->api_calls_per_month,
            'has_competitor_analysis' => $request->has_competitor_analysis ? 1 : 0,
            'has_pdf_export' => $request->has_pdf_export ? 1 : 0,
            'has_csv_export' => $request->has_csv_export ? 1 : 0,
            'has_white_label' => $request->has_white_label ? 1 : 0,
            'has_api_access' => $request->has_api_access ? 1 : 0,
            'has_priority_support' => $request->has_priority_support ? 1 : 0,
            'is_active' => $request->is_active ? 1 : 0,
            'sort_order' => $request->sort_order,
        ]);
    
        $notification = array(
            'message' => 'Plan created successfully',
            'alert-type' => 'success'
        );
    
        return redirect()->route('all.plans')->with($notification);  
    }
    // End Method 

    public function EditPlans($id){
        $plans = Plan::find($id);
        return view('admin.backend.plan.edit_plan',compact('plans'));

     }
     // End 
     
     public function UpdatePlans(Request $request){
        $plan = Plan::findOrFail($request->id);

        if (!$plan) {
            abort(404, 'Plan not found');
        }
    
        $plan->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
            'currency' => $request->currency,
            'analyses_per_month' => $request->analyses_per_month,
            'projects_limit' => $request->projects_limit,
            'team_members_limit' => $request->team_members_limit,
            'api_calls_per_month' => $request->api_calls_per_month,
            'has_competitor_analysis' => $request->has_competitor_analysis ? 1 : 0,
            'has_pdf_export' => $request->has_pdf_export ? 1 : 0,
            'has_csv_export' => $request->has_csv_export ? 1 : 0,
            'has_white_label' => $request->has_white_label ? 1 : 0,
            'has_api_access' => $request->has_api_access ? 1 : 0,
            'has_priority_support' => $request->has_priority_support ? 1 : 0,
            'is_active' => $request->is_active ? 1 : 0,
            'sort_order' => $request->sort_order,
        ]);
    
        return redirect()->route('all.plans')->with([
            'message' => 'Plan updated successfully',
            'alert-type' => 'success'
        ]); 
    }
     // End Method 

     public function DeletePlans($id){
        Plan::find($id)->delete();

        $notification = array(
            'message' => 'Plans Deleted successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

     }
     // End Method 


}
