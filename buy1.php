<?php 
defined('SYSPATH') or die('No direct script access.');

class Controller_buy extends Controller {

    private $getRequest;

    public function __construct(Request $request) {
        parent::__construct($request);
        $this->getRequest = $request;
        //test1
    }

    /**
     * Redirect index action 
     */
    public function action_index() {
        $this->request->redirect($this->request->uri(
                        array('action' => 'ddbuyrecord')), 301);
    }
    
    
    /**
     * @desc Create Lead Purchase records
     * @since Jan 29, 2014
     * @author Joel
     * */
    public function action_addbuyrecord() {

	 
    	$required = array(
    			'vid' => '1',
    			'count' => '1',
    			'key' => '1',
    			'login' => '1',
    	);

    	
    	if (isset($_REQUEST)) {
    		foreach ($_REQUEST as $key => $value) {
    			switch ($key) {
    				case 'vid':
    				case 'count':
    				case 'key':
    				case 'login':    						    					
    					if (strlen($value) > 0) {
    						$required[$key] = '';
    						$required[$key] = '';
    					}
    					break;
    				default:
    					break;
    			}
    		}
    	}
    
    	$error = null;
    	foreach ($required as $key => $value) {
    		if ($value == '1') {
    			$missing_str .= "Missing " . $key . "|";
    			$error = '99';
    		}
    	}
    	if ($error) {
    		print "rejected|" . $missing_str;
    		exit();
    	}
    	
    	$leadsource = ORM::factory('lead_source')
    		->where('api_key', '=', trim($_REQUEST['key']))
    		->where('login', '=', trim($_REQUEST['login']) )
    		->find();
    	
    	if (!$leadsource->loaded()){
    		print "rejected|Invalid Credintials";
    		exit();
    	}
    	
    	$user = ORM::factory('user')->where('vemma_id', '=', trim($_REQUEST['vid']))->find();
    	if (!$user->loaded()){
    		print "rejected|Invalid User";
    		exit();
    	} 
    	
    	if (isset($_REQUEST['test']) && $_REQUEST['test'] == '1'){
    		print "test|accepted";
    		exit();
    	}
    	
 
		$ormLeadPurchase = ORM::factory('lead_purchase');
		$ormLeadPurchase->user_id = $user->id;
		$ormLeadPurchase->lead_count = $_REQUEST['count'];
		$ormLeadPurchase->lead_source_id = $leadsource->id;
		$ormLeadPurchase->lead_type_id = '2';
		$ormLeadPurchase->date_time = date("Y-m-d H:i:s");
		$ormLeadPurchase->status_id = '';
		$ormLeadPurchase->lead_purchase_day = date('j');
		$ormLeadPurchase->type = 'single';
		if (isset($_REQUEST['type']) && trim($_REQUEST['type']) != ''){
			$ormLeadPurchase->type = $_REQUEST['type'];
		}
		$ormLeadPurchase->save();
		
		if (isset($ormLeadPurchase->id)){
			print "accepted|" .  $ormLeadPurchase->id;
		}else{
			print "rejected|failed to write order";
		}
    }

    
    /**
     * @desc Create Lead Purchase records
     * @since Jan 29, 2014
     * @author Joel
     * */
    public function action_getdropped() {
    
    
    	$required = array(
    			'vid' => '1',
    			'key' => '1',
    			'login' => '1',
    	);
    
    	 
    	if (isset($_REQUEST)) {
    		foreach ($_REQUEST as $key => $value) {
    			switch ($key) {
    				case 'vid':
    				case 'key':
    				case 'login':
    					if (strlen($value) > 0) {
    						$required[$key] = '';
    					}
    					break;
    				default:
    					break;
    			}
    		}
    	}
    
    	$error = null;
    	foreach ($required as $key => $value) {
    		if ($value == '1') {
    			$missing_str .= "Missing " . $key . "|";
    			$error = '99';
    		}
    	}
    	if ($error) {
    		print "rejected|" . $missing_str;
    		exit();
    	}
    	 
    	$leadsource = ORM::factory('lead_source')
    	->where('api_key', '=', trim($_REQUEST['key']))
    	->where('login', '=', trim($_REQUEST['login']) )
    	->find();
    	 
    	if (!$leadsource->loaded()){
    		print "rejected|Invalid Credintials";
    		exit();
    	}
    	 
    	$user = ORM::factory('user')->where('vemma_id', '=', trim($_REQUEST['vid']))->find();
    	if (!$user->loaded()){
    		print "rejected|Invalid VID/User";
    		exit();
    	}
    	 

    	$leads = ORM::factory('lead')
    		->where('user_id', '=', $user->id)
    		->where('lead_source_id', '=', $leadsource->id)
    		->find_all()->as_array();
    
    	
    	if (count($leads)){
    		$nlds = array();
    		foreach($leads as $row){
    			$nlds[] = array(
    				'first_name' => $row->first_name,
    				'last_name' => $row->last_name,			
    				'email' => $row->email_address,
    				'phone' => $row->phone1,
    			);
    		}
    		
    		$result = json_encode(array("count"=> count($nlds), "records" => $nlds));
    		print $result;
    	}else{
    		print json_encode(array("count"=>"0"));
    	}
    }
    


} // End 
