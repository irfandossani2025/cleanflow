<?php
namespace App\Support;

use App\Models\Business;
use Illuminate\Http\Request;

final class Workspace
{
    public static function business(Request $request): Business
    {
        $business = $request->user()?->business;
        abort_unless($business, 403, 'Select or create a business workspace first.');
        return $business;
    }

    public static function assertBusiness(object $model, Business $business): void
    {
        abort_unless(isset($model->business_id) && (int) $model->business_id === (int) $business->id, 404);
    }
}
