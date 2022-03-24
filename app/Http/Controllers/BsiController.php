<?php
namespace App\Http\Controllers;
use Exception;
use App\Helpers\ResponseFormatter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class BsiController extends Controller
{
    public function index(){
        $response=('https://my-json-server.typicode.com/Fakhri-Nurrohman/transtory-transactionbsi/transactions?_sort=trxDate&_order=desc');
        $data = json_decode(file_get_contents($response), true);
        return response()->json($data);
    }

    public function transaction(Request $request)
    {
        $accountNum=$request->accountNum;
        if(!$accountNum){
            return ResponseFormatter::error(
                [
                    'accountNum'=>$request->all()
                ],
                "Account Num cannot be empty",
                401
            );
        }

        try{
            $client=new Client();
            $url=('https://my-json-server.typicode.com/Fakhri-Nurrohman/transtory-transactionbsi/transactions?q='.$accountNum.'&_sort=trxDate&_order=desc');

            $response=$client->request('GET',$url,[
                'json'=>json_decode(file_get_contents($url)),
            ]);

            return ResponseFormatter::success(
                [
                    'response'=>json_decode($response->getBody())
                ],
                "Succesful Request"
            );

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                return ResponseFormatter::error(
                    [
                        'message' => json_decode($response->getBody())->responseDescription,
                    ],
                    "Failed Request",
                    $response->getStatusCode(),
                );
            }

        } catch (Exception $e){
            return ResponseFormatter::error(
                [
                    'error'=>$e,
                    'message'=>$e->getMessage(),
                ],
                "Failed Request"
            );
        }
    }
}
