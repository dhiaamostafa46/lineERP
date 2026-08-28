<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB;

class LogApiRequestResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       $startTime = microtime(true);

        $response = $next($request);

      

        $responseBody = self::truncate_mask($response);

        DB::table('api_req_resp')->insert([
            'method'        => $request->method(),
            'endpoint'           => $request->fullUrl(),
            '_request'  => json_encode($request->except(['password', 'token']),JSON_UNESCAPED_UNICODE),
            '_response' => $responseBody,
            'status'   => $response->status(),
            'ip'            => $request->ip(),
            'user_id'       => auth()->id(),
            'duration_ms'  => (microtime(true) - $startTime) * 1000,
            'created_at'    => now(),
        ]);

        return $response;
          // log only errors
        //if ($response->status() >= 400) {
         
            //}
         
    }
    function truncate_mask($response)
    {
          $data = json_decode($response->getContent(), true);

        $truncated = array_slice($data, 0, 2); // first 10 items only

        $responseBody = json_encode($truncated);
        $result = self::maskSensitive( $responseBody );
        return  $result;
    }
    function maskSensitive($data)
    {
        $sensitive = ['basic_salary','net_wage','total_deducts', 'bank_iban','identity_no'];

        // foreach ($sensitive as $field) 
        //     {
                
        //        if (isset($data[$field]))
        //          {
        //             $data[$field] = '***';
                     
        //           }
                  
               
        //      }
        
    //   $maskedResponse="";
    //     if (isset($data['payrolls']) && is_array($data['payrolls'])) 
    //                 {
    //                       //$maskedResponse = json_encode($data);
    //                     foreach ($data['payrolls'] as $payroll) {
    //                          if (isset($payroll['basic_salary'])) 
    //                             {
    //                             //$maskedResponse = json_encode($data);
    //                               $payroll['basic_salary'] = '***';
    //                             }
    //                       }
    //                       $maskedResponse = json_encode($data);
    //                 }
                   
    return  $data;
   }
}
