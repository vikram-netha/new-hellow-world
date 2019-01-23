<link href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700|Open+Sans+Condensed:700" rel="stylesheet" type="text/css" />
<?php
    define( '_JEXEC', 1 );
    define('JPATH_BASE', str_replace('/cron','',dirname(__FILE__)) );
    define( 'DS', DIRECTORY_SEPARATOR );
    
    /* To verify the IPADDRESS */
    //require_once ( JPATH_BASE .DS.'CronSecurityCheck.php' );
      
    /* Required Files */
    require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
    require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
    /* To use Joomla's Database Class */
    require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'factory.php' );
    /* Create the Application */
    $mainframe =& JFactory::getApplication('site');

    //For the PDF file
    require_once ( JPATH_BASE .DS.'libraries'.DS.'tcpdf'.DS.'config'.DS.'lang'.DS.'eng.php' );
    require_once ( JPATH_BASE .DS.'libraries'.DS.'tcpdf'.DS.'tcpdf.php' );
    
    //For the CSV file
    require_once ( JPATH_BASE .DS.'Classes'.DS.'PHPExcel.php' );
    require_once ( JPATH_BASE .DS.'Classes'.DS.'PHPExcel'.DS.'IOFactory.php' );

    ini_set('zlib.output_compression','Off');
    
    
    /* Added for setting the PATH start */
        //$root = str_replace(basename($_SERVER['SCRIPT_NAME']),'',$_SERVER['SCRIPT_NAME']);
    /* Added for setting the PATH end */
    //$path =   $_SERVER['DOCUMENT_ROOT'].$root;

   
    /* compliance report code start */
    $db =& JFactory::getDBO();
    //$sql_masters = "SELECT R.id,R.masterid,R.master,R.admin,R.dm,R.m,R.manager_specific,email_freq.date,email_freq.weeks,R.myvendors,R.corporatevendors,R.coworkervendors FROM `jos_cam_master_compliancereport` as R, `jos_cam_master_email_compliance_status` as email_freq, LEFT JOIN `jos_cam_master_compliancereport_emails` as specific_emails_tbl ON R.id = specific_emails_tbl.comp_report_id  where R.id = email_freq.comp_report_id AND R.report_switch='manager'";
    $sql_masters_qry = "SELECT R.id,R.masterid,R.master,R.admin,R.dm,R.m,R.manager_specific,email_freq.date,email_freq.weeks,R.myvendors,R.corporatevendors,R.coworkervendors,
        GROUP_CONCAT(DISTINCT specific_emails_tbl.email) as specific_email, CONCAT_WS(' ', User.name, User.lastname) AS manager_name, User.user_type, User.dmanager, User.accounttype  
        FROM `jos_cam_master_compliancereport` as R  
        JOIN `jos_cam_master_email_compliance_status` AS email_freq ON R.id = email_freq.comp_report_id  
        JOIN `jos_users` AS User ON R.masterid = User.id  
        LEFT JOIN `jos_cam_master_compliancereport_emails` as specific_emails_tbl ON R.id = specific_emails_tbl.comp_report_id 
        WHERE R.id = email_freq.comp_report_id AND R.report_switch='manager'
        GROUP BY `R`.`id` 
        ORDER BY `R`.`id` ASC, GROUP_CONCAT(DISTINCT `specific_emails_tbl`.`id`) ASC";
    //echo $sql_masters_qry;exit;
    $db->setQuery($sql_masters_qry);//, LEFT JOIN `jos_cam_master_compliancereport_emails` as specific_emails_tbl ON R.id = specific_emails_tbl.comp_report_id 
    $reports = $db->loadObjectList();
    echo '<pre/>'; 
    $today = date('Y-m-d');
    $model = new managerActivityReport();
  if(isset($reports) && !empty($reports) ){
    foreach ($reports as $key_report => $val_report){
        echo ' <hr/>';
        print_r($reports[$key_report]);
        $days = 0;
        $days_multiply_weeks = 0;
        
        if(!empty($val_report->date)){
            
            $dbdate = explode(' ',$val_report->date);
            $date_difference = strtotime($today) - strtotime($dbdate[0]);
            $days_total = $date_difference / 86400;
            $days = round($days_total);
            echo 'Days '.$days;
            if($val_report->weeks == 2){ $days_multiply_weeks = 30; }
            else if($val_report->weeks == 4){ $days_multiply_weeks = 60; }
            else if($val_report->weeks == 8){ $days_multiply_weeks = 90; }    
            //$reminder = $days_multiply_weeks - $days;
            $reminder = $days % $days_multiply_weeks;
        }else{
            $reminder = 0;
        }
        echo ' <br/> Reminder '.$reminder;
        //if($val_report->id == 184){ $reminder = 0; } else{ $reminder = 1;} //357 local, 184 live
        
        if( $reminder == '0' && $val_report->weeks > '0' || $days == 31  )
        {
            $final_managers_list = array();
            $report_id = $val_report->id;
            
            $managers_list = array();
            $comp_info = array();
            $successMailChk = 0;
            
            //To get all the managers of his compnay 
            $managers_list = $model->getmanager_accounts($val_report->masterid);
            if(isset($managers_list) && !empty($managers_list) ){
                
                if(isset($val_report->manager_specific) && !empty($val_report->manager_specific) ){
                   $final_managers_list[] = $val_report->manager_specific;
                }

               foreach($managers_list as $key_manager => $val_managers){
                   $manager_detail_info = $model->getUserIdInfo($val_managers);

                   if($val_report->master == 1 && $manager_detail_info->user_type == 13 && (strcasecmp($manager_detail_info->accounttype,'master')== 0)){
                       $final_managers_list[] = $manager_detail_info->id;
                   }
                   else if($val_report->admin == 1 && $manager_detail_info->user_type == 13 && empty($manager_detail_info->accounttype)){
                        $final_managers_list[] = $manager_detail_info->id;
                   }
                   else if($val_report->dm == 1 && $manager_detail_info->user_type == 12 && (strcasecmp($manager_detail_info->dmanager,'yes')== 0)){
                       $final_managers_list[] = $manager_detail_info->id;
                   }
                }

               $final_managers_list = array_unique($final_managers_list);
               echo '<h3>Managers list : </h3>'; print_r($final_managers_list);
               /* To Send The Report Manager Activity Report to Managers start */
               foreach($final_managers_list as $key_final_manager => $val_final_managers){
                   echo '<br/> Manager is '.$key_final_manager.')'; print_r($val_final_managers);
                   if(sendManagerReport($val_final_managers, $report_id)){
                       $successMailChk++;
                   }
               }
               /* To Send The Report Manager Activity Report to Managers end */
            }
            
            /* For Send The Report Manager Activity Report Specific Emails start */
            if(isset($val_report->specific_email) && !empty($val_report->specific_email) ){
                $specific_emails_list = explode(',',$val_report->specific_email);
                foreach ($specific_emails_list as $key_specific_email => $val_specific_email)
                {
                    if(sendManagerReport($val_report->masterid, $report_id, $val_specific_email)){
                        $successMailChk++;
                    }
                }
            }
            /* For Send The Report Manager Activity Report Specific Emails end */
            
            if($successMailChk > 0){
                //Update Report final updated date from table jos_cam_master_email_compliance_status
                $update_lastsent_date = $model->updateReportMailLastSendDate($report_id);
                
            }
        }
        
    }
  }//Checking reports are empty or not
    
  
     
function sendManagerReport($manager_id = '', $report_id = '', $specificmail = '')
{
    /* Added for setting the PATH start */
        $root = str_replace(basename($_SERVER['SCRIPT_NAME']),'',$_SERVER['SCRIPT_NAME']);
    /* Added for setting the PATH end */
    $path =   $_SERVER['DOCUMENT_ROOT'].$root;
    
    $model = new managerActivityReport();
    $manager_info = $model->getUserInfo($manager_id);
    $company_name = $model->getManagerCompnayNameInfo($manager_id);
   
    $message = $model->getpreferredvendors_list_manager($manager_id, $report_id);
    $vendor_data = $message;
    
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $i = 2;
    $j = 3;
		
    //echo '<pre>'; print_r($value); echo '</pre>';
     $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $key);
     $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
    //$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(-1);
    $styleArray = array(
        'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF'),
    ));
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
         
        $objPHPExcel->getActiveSheet()->getStyle('A'.$j)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '073763'))));

        $objPHPExcel->getActiveSheet()->setCellValue('A'.$j, 'Name');
        //$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$j)->applyFromArray($styleArray);
        $char = '66'; //(Ascii value of B)

        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '073763'))));

                $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$j, 'Account Type');
                    //$objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray($styleArray);
                $char++;

        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '073763'))));

                $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$j, 'Last Login Date');
                        //$objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray($styleArray);
                $char++;

        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffe599'))));
        
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, 'Requests Submitted');
                        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->mergeCells("D".$i.":E".$i);

                $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffe599'))));

                $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$j, 'past 60d');
                        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->getFont()->setBold(true);
                $char++;
			
        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffe599'))));
		
			
                $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$j, 'past year');
                        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->getFont()->setBold(true);
                $char++;
			
        $objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'd9ead3'))));


                    $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, 'Proposals Received');
                    $objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->mergeCells("F".$i.":G".$i);
                    
                $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'd9ead3'))));
                $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$j, 'past 60d');
                        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->getFont()->setBold(true);
                $char++;
			
        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'd9ead3'))));
		
                $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$j, 'past year');
                        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->getFont()->setBold(true);
                $char++;
			
        $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ead1dc'))));
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, 'Vendor List*');
                    $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->mergeCells("H".$i.":I".$i);
                            
		$objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ead1dc'))));
                $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$j, 'Compliant');
                        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->getFont()->setBold(true);
                $char++;				
			
        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ead1dc'))));
			
                $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$j, 'Non-Compliant');
                        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->getFont()->setBold(true);
                $char++;
			
        $objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'cfe2f3'))));
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, 'Vendors Under Contract');
                    $objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getFont()->setBold(true);

                $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'cfe2f3'))));
                $objPHPExcel->getActiveSheet()->mergeCells("J".$i.":K".$i);
                $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$j, 'Compliant');
                        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->getFont()->setBold(true);
                $char++;
			
        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'cfe2f3'))));
                $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$j, 'Non-Compliant');
                        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->getFont()->setBold(true);
                $char++;
                
        $objPHPExcel->getActiveSheet()->mergeCells("L".$i.":N".$i);
        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'cfe2f3'))));
        $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$j, 'Current Contracts(In Dollars)');
        $objPHPExcel->getActiveSheet()->getStyle(chr($char).$j)->getFont()->setBold(true);
        
        foreach($vendor_data as $key=>$value)
        {
            $count = count($value);
            $i = $i + $count + 2;
            $k = $j+1; 
           //for($last=0; $last<count($value); $last++){
           //echo '<pre>'; print_r($value); echo '</pre>';
                   $date_final=explode(' ',$value['last_login']);
                   //$today = date('Y-m-d'); 
             $after30day=strtotime(date('Y-m-d', strtotime('+30 days', strtotime($date_final[0])))); 
            //echo  $30day_after_final = strtotime($after30day);
           $today = strtotime(date('Y-m-d'));
            //echo 'anand';exit;
            //echo '<pre>docs_permission'; print_r($value[$last]['name']); echo '</pre>'; exit;
            //$exp = explode('MYVC',$value[$last]);
            //if($value[$last]){
            $redStyleArray = array(
                'font'  => array(
                    'color' => array('rgb' => 'FF0022'),
            ));


            $objPHPExcel->getActiveSheet()->setCellValue('A'.$k, $value['name']);

            $char = '66'; //(Ascii value of B)
			
					
            $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$k,  $value['account_type']);
            $char++;


            if ($after30day < $today)
            {
            //	$exp[11] = str_replace('red','',$exp[11]);  
                    $objPHPExcel->getActiveSheet()->getStyle(chr($char).$k)->applyFromArray($redStyleArray);							
            }

            $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$k, $date_final[0]);					
            $char++;



            if ($value['request_6odays']=='0')
            {
                    $objPHPExcel->getActiveSheet()->getStyle(chr($char).$k)->applyFromArray($redStyleArray);							
            }

            $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$k, $value['request_6odays']);					
            $char++;



            if ($value['request_365days']=='0')
            {
                    //$exp[4] = str_replace('red','',$exp[4]);  
                    $objPHPExcel->getActiveSheet()->getStyle(chr($char).$k)->applyFromArray($redStyleArray);
            }

            $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$k, $value['request_365days']);
            $char++;



            if ($value['proposals_60days']=='0')
            {
                    //$exp[5] = str_replace('red','',$exp[5]);  
                    $objPHPExcel->getActiveSheet()->getStyle(chr($char).$k)->applyFromArray($redStyleArray);
            }

            $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$k, $value['proposals_60days']);
            $char++;



            if ($value['proposals_365days']=='0')
            {
                    //$exp[6] = str_replace('red','',$exp[6]);  
                    $objPHPExcel->getActiveSheet()->getStyle(chr($char).$k)->applyFromArray($redStyleArray);
            }

            $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$k, $value['proposals_365days']);
            $char++;



            if ($value['myven_com']=='0')
            {
                    //$exp[7] = str_replace('red','',$exp[7]);  
                    $objPHPExcel->getActiveSheet()->getStyle(chr($char).$k)->applyFromArray($redStyleArray);
            }

            $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$k, $value['myven_com']);
            $char++;				



            if ($value['myven_nonc'] >'0')
            {
                    //$exp[9] = str_replace('red','',$exp[9]);  
                    $objPHPExcel->getActiveSheet()->getStyle(chr($char).$k)->applyFromArray($redStyleArray);
            }

            $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$k, $value['myven_nonc']);
            $char++;



            if ($value['myven_com_cont']=='0')
            {
                    //$exp[8] = str_replace('red','',$exp[8]);  
                    $objPHPExcel->getActiveSheet()->getStyle(chr($char).$k)->applyFromArray($redStyleArray);
            }

            $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$k, $value['myven_com_cont']);
            $char++;

            if ($value['myven_nonc_cont'] >'0')
            {
                    //$exp[0] = str_replace('red','',$exp[0]);  
                    $objPHPExcel->getActiveSheet()->getStyle(chr($char).$k)->applyFromArray($redStyleArray);
            }

            $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$k, $value['myven_nonc_cont']);
            $char++;
            if ($value['contract_amount']=='0')
            {
                    //$exp[1] = str_replace('red','',$exp[1]);  
                    $objPHPExcel->getActiveSheet()->getStyle(chr($char).$k)->applyFromArray($redStyleArray);

            }

            $objPHPExcel->getActiveSheet()->setCellValue(chr($char).$k, $value['contract_amount']);

                    $k++;			
                    //}				
                //}
                $i++;
            $j++;
        }
        
            //$objPHPExcel->getActiveSheet()->setCellValue('A'.$j, 'Requests Submitted');
            //	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
            //	$objPHPExcel->getActiveSheet()->mergeCells("A".$j.":B".$j":C".$j":D".$j":E".$j":F".$j);
            //echo $j; exit;$objPHPExcel->getActiveSheet()->setCellValue('A'.$j, 'Name');
            $jk=$j+2;
            $objPHPExcel->getActiveSheet()->mergeCells("B".$jk.":L".$jk);
            $objPHPExcel->getActiveSheet()->getCell('B'.$jk)->setValue("*Only includes Vendors listed within that Manager's personal 'MY VENDORS' list. These vendors are also included in that manager's automated compliance report.  If you need assistance setting up automated compliance reports, please email support@myvendorcenter.com");
            $objPHPExcel->getActiveSheet()->getRowDimension($jk)->setRowHeight(50);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$jk)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$jk)->getFont()->setBold(true);
		
            
        $today = date('m-d-Y H:i:s');
        $today_explode = explode(' ',$today);
        
        if(isset($specificmail) && !empty($specificmail)){
            $to_email_report = $specificmail;
            $username = (isset($company_name) && !empty($company_name))?$company_name:$manager_info->name.' '.$manager_info->lastname;
            $user_file_name = $specificmail;
        }
        else{
            $to_email_report = $manager_info->email;
            $username = $manager_info->name.' '.$manager_info->lastname;
            $user_file_name = $manager_info->name.' '.$manager_info->lastname;
        }
        
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$title = 'ManagerActivity_'.$manager_info->id.'-'.$user_file_name.'-'.$today_explode[0].'.xlsx'; //set title
        $title = 'ManagerActivity_'.$report_id.'-'.$user_file_name.'-'.$today_explode[0].'.xls'; //set title
            
        $attachment = $path."components/com_camassistant/assets/compliance_reports/".$title;
        $objWriter->save($path.'components/com_camassistant/assets/compliance_reports/'.$title);
                        
                $reportmessage = $model->reportmessage();  
  		$body =  $reportmessage;
		$body = str_replace('[Manager Full Name]',$username,$body);
		$body = str_replace('[TIME]',date("h:i A", strtotime($today_explode[1])),$body);
		$body = str_replace('[DATE]',$today_explode[0],$body);

		$mailfrom = 'support@myvendorcenter.com';
		$from_name = 'MyVendorCenter';
                $subject = "Your personal Manager Activity Report";
                
                if(isset($to_email_report) && !empty($to_email_report))
                {
                    echo '<br/> To : '.$to_email_report;
                    echo '<br/> Body is : <br/>';echo $body;
                    //$successMail = 1; //Local 
                    $successMail = ''; //Live 
                    //$to = "rize.cama@gmail.com";
                    
                    //Live 
//                    $successMail =JUtility::sendMail($mailfrom, $from_name, $to_email_report, $subject, $body,$mode = 1, $cc=null, $bcc=null, $attachment, $replyto=null, $replytoname=null);
//                    $to_manager_email = 'manageremails@myvendorcenter.com';
//                    $successMail =JUtility::sendMail($mailfrom, $from_name, $to_manager_email, $subject, $body,$mode = 1, $cc=null, $bcc=null, $attachment, $replyto=null, $replytoname=null);
                    
                    //Local 
                    $to_rize_gmail = 'rize.cama@gmail.com';
                    $successMail =JUtility::sendMail($mailfrom, $from_name, $to_rize_gmail, $subject, $body,$mode = 1, $cc=null, $bcc=null, $attachment, $replyto=null, $replytoname=null);
                    if($successMail){ 
                         //To Maintatin the logs of mails sent based on report id.
                        $update_notification_log = $model->updateNotificationMailLogInfo($report_id,$to_email_report);
                        echo 'Update completed Success <hr/>';
                        return true;
                    }
                }
                return false;
            
            /* To download the CSV Report 
                if(!empty($root) && $root != '/'){
                    header('Location: '.$root.'/components/com_camassistant/assets/compliance_reports/'.$title);
                }else{
                    header('Location: /components/com_camassistant/assets/compliance_reports/'.$title);
                }
            */
    }
                        
    class managerActivityReport
    {
        function __construct()
	{
            $db = & JFactory::getDBO();
	}
        
        /* Function For Manager Information Start */         
        public function getUserInfo($manager_id = '')
        {
            $db = & JFactory::getDBO();
            $sql_userinfo_qry = "SELECT * from jos_users where `id` = '".$manager_id."'";
            $db->Setquery($sql_userinfo_qry);
            $vendor_userinfo_data = $db->loadObject();
            return $vendor_userinfo_data;
        }
        /* Function For Manager Information End */         
        
        /* Function For Master Start */ 
        private function getmastermanagers($manager_id = '')
        {
            $db=&JFactory::getDBO();
            $user =$this->getUserInfo($manager_id);
            $sql1 = "SELECT firmid from #__cam_masteraccounts where masterid=".$user->id." ";
            $db->Setquery($sql1);
            $subfirms = $db->loadObjectlist();

            if($subfirms){
                    for( $a=0; $a<count($subfirms); $a++ ){
                                    $firmid1[] = $subfirms[$a]->firmid;
                                    $sql = "SELECT id from #__cam_camfirminfo where cust_id=".$subfirms[$a]->firmid." ";
                                    $db->Setquery($sql);
                                    $companyid[] = $db->loadResult();
                            }
            }
            
            $sql = "SELECT id from #__cam_camfirminfo where cust_id=".$user->id." ";
            $db->Setquery($sql);
            $companyid[] = $db->loadResult();
            if($companyid){
                    for( $c=0; $c<count($companyid); $c++ )	{
                                            $sql_cid = "SELECT cust_id from #__cam_customer_companyinfo where comp_id=".$companyid[$c]." ";
                                            $db->Setquery($sql_cid);
                                            $managerids = $db->loadObjectList();
                                                    if($managerids) {
                                                            foreach( $managerids as $last_mans){
                                                                    $total_mangrs[] = $last_mans->cust_id ;
                                                            }
                                                    }
                   }
            }

            if($firmid1 && $total_mangrs ){
                $total_mangrs = array_merge($total_mangrs,$firmid1); 
            }

            /*if($firmid1){
                $total_mangrs = $firmid1;
            }
             */
            $userid=array($user->id);
            if($total_mangrs){
            $total_mangrs = array_merge($userid,$total_mangrs); 
            } else {
                    $total_mangrs[] = $user->id; 
            }
            return $total_mangrs;
	}
        /* Function For Master End */ 
        
        /* Function For Camfirm Administrator start */ 
        private function gettotalmanagersofcamfirm($manager_id = ''){
		$db = JFactory::getDBO();
		 $user =$this->getUserInfo($manager_id);
		$query = "SELECT id FROM #__cam_camfirminfo WHERE cust_id=".$user->id;
		$db->setQuery($query);
		$comp_id = $db->loadResult();
		$userid=array($user->id);
		$query_mans = "SELECT cust_id from #__cam_customer_companyinfo where comp_id = ".$comp_id." ";
		$db->setQuery($query_mans);
		$Managers_list = $db->loadObjectList();
		
		foreach( $Managers_list as $cf_mans){
			$total_mangrs[] = $cf_mans->cust_id ;
		}
		if($total_mangrs){
			$totalcust_id1 = array_merge($userid,$total_mangrs); 
		} else {
			$totalcust_id1[] = $user->id; 
		}
		return $totalcust_id1; 	
	}
	/* Function For Camfirm Administrator End */ 
        
        /* Function For District Manager Start */ 
        private function gettotalmanagersofdm($manager_id = ''){
		$db = JFactory::getDBO();
		 $user =$this->getUserInfo($manager_id);
		$dmmanagers = "SELECT DISTINCT managerid FROM #__cam_invitemanagers WHERE dmanager=".$user->id;
		$db->setQuery($dmmanagers);
		$dm_managers = $db->loadObjectlist();
						
		for($i=0; $i<count($dm_managers);$i++){
			$query = "SELECT id from #__users where id='".$dm_managers[$i]->managerid."'" ;
			$db->setQuery($query);
			$total_mangrs[]=$db->loadResult();
		}
				/*if($Managers_list){
		foreach( $Managers_list as $cf_mans)
			{
				$total_mangrs[] = $cf_mans->id ;
			}
			}*/
	
		$userid=array($user->id);		
		if($total_mangrs){
			$totalcust_id1 = array_merge($userid,$total_mangrs); 
		} else {
			$totalcust_id1[] = $user->id; 
		}
                return $totalcust_id1; 		
	}
        /* Function For District Manager End */ 
        
        public function getmanager_accounts($manager_id = ''){
		$user =$this->getUserInfo($manager_id);
		if($user->user_type == 13 && $user->accounttype != 'master') {
			$totalmanagers = $this->gettotalmanagersofcamfirm($manager_id);
		} else if($user->dmanager == 'yes'){
			$totalmanagers = $this->gettotalmanagersofdm($manager_id) ;	
		} else if($user->user_type ==13 && $user->accounttype == 'master') {
                    $totalmanagers = $this->getmastermanagers($manager_id) ;
		}
                return $totalmanagers;
	}
        
        private function getmasterfirm_owner($manager){
		$user =$this->getUserInfo($manager_id);
		$db=&JFactory::getDBO();
		$query_user = "SELECT user_type,accounttype FROM #__users WHERE id=".$manager." ";
		$db->setQuery($query_user);
		$user_data = $db->loadObject();
		$user_type = $user_data->user_type ;
		$accounttype = $user_data->accounttype;		
		
			if($user_type == '12'){
				$query_c = "SELECT comp_id FROM #__cam_customer_companyinfo WHERE cust_id=".$manager." ";
				$db->setQuery($query_c);
				$cid = $db->loadResult();	

				$camfirmid = "SELECT cust_id FROM #__cam_camfirminfo WHERE id=".$cid." ";
				$db->setQuery($camfirmid);
				$camfirm = $db->loadResult();

				$masterid = "SELECT masterid FROM #__cam_masteraccounts WHERE firmid=".$camfirm." ";
				$db->setQuery($masterid);
				$master = $db->loadResult();
					if($master)
					$master = $master ;
					else
					$master = $camfirm ;
			} elseif($user_type == '13' && $accounttype!='master'){
				$masterid = "SELECT masterid FROM #__cam_masteraccounts WHERE firmid=".$manager." "; 
				$db->setQuery($masterid);
				$master = $db->loadResult();
					if($master)
					$master = $master ;
					else
					$master = $manager ;
			} else {
				$master = $manager;
			}	
			return $master ;
	}
        
        //Function to get vendor industries
	private function getvendorindustries($vendorid){
		$db = JFactory::getDBO();
		 $v_inds = "SELECT U.industry_id, V.industry_name FROM `jos_cam_vendor_industries` as U, jos_cam_industries as V where U.industry_id=V.id and U.user_id=".$vendorid." order by V.industry_name ASC " ;
		$db->Setquery($v_inds);
		$vendor_industries = $db->loadObjectList();
		//echo '<pre>';print_r($vendor_industries);exit;
		return $vendor_industries;
	}
        
        public function getpreferredvendors_list_manager($manager_id = '', $report_id=''){
            	$db = JFactory::getDBO();
		//$report_id = JRequest::getVar('report_id','');
           
		/* $query_report = "SELECT masterid from #__cam_master_compliancereport where id='".$report_id."'";
		$db->setQuery($query_report);
		$report_manager_id = $db->loadResult();*/
                $manager_accounts  =	$this->getmanager_accounts($manager_id);
                
		foreach( $manager_accounts as $report_manager_id){

                    $query_user = "SELECT name,lastname,user_type,accounttype,dmanager,lastvisitDate from #__users where id='".$report_manager_id."'";
                    $db->setQuery($query_user);
                    $report_user_info = $db->loadObject();

                    $final_manager_data['name']=$report_user_info->name.' '.$report_user_info->lastname;
                    $final_manager_data['last_login']=$report_user_info->lastvisitDate;

                    if($report_user_info->user_type=='13' && $report_user_info->accounttype=='master'){
                            $final_manager_data['account_type']='Master';
                    } else if($report_user_info->user_type=='13' && $report_user_info->accounttype!='master'){
                            $final_manager_data['account_type']='Admin';
                    } else if($report_user_info->user_type=='12' && $report_user_info->dmanager=='yes'){
                            $final_manager_data['account_type']='District Manager';
                    } else {
                            $final_manager_data['account_type']='Standard Manager';
                    }

                    $today = date('Y-m-d');
                    $today1 = date('m-d-Y');
                    $daysbefore60=date('m-d-Y', strtotime('-60 days', strtotime($today)));
                    $daysbefore_oneyear=date('m-d-Y', strtotime('-1 year', strtotime($today)));

                    $final_manager_data['request_6odays'] = '';
                    $final_manager_data['request_365days'] = '';
                    $final_manager_data['proposals_60days'] = '';
                    $final_manager_data['proposals_365days'] = '';
		
                    $query_recards_60 = "SELECT count(id) FROM #__cam_rfpinfo WHERE str_to_date( createdDate, '%m-%d-%Y' ) > str_to_date( '$daysbefore60', '%m-%d-%Y' )
                        AND str_to_date( createdDate, '%m-%d-%Y' ) <= str_to_date('$today1', '%m-%d-%Y' ) and rfp_type!='draft' and cust_id='".$report_manager_id."'";
                    $db->setQuery($query_recards_60);
                    $final_manager_data['request_6odays'] = $db->loadResult();

                    $query_recards_365 = "SELECT count(id) FROM #__cam_rfpinfo WHERE str_to_date( createdDate, '%m-%d-%Y' ) > str_to_date( '$daysbefore_oneyear', '%m-%d-%Y' )
                        AND str_to_date( createdDate, '%m-%d-%Y' ) <= str_to_date('$today1', '%m-%d-%Y' ) and rfp_type!='draft' and cust_id='".$report_manager_id."'";
                    $db->setQuery($query_recards_365);
                    $final_manager_data['request_365days'] = $db->loadResult();

                    $v_recards_60days = "SELECT count(CP.id) as proposals_count FROM #__cam_rfpinfo as CR INNER JOIN #__cam_vendor_proposals as CP ON CR.id = CP.rfpno WHERE str_to_date( CR.createdDate, '%m-%d-%Y' ) > str_to_date( '$daysbefore60', '%m-%d-%Y' )
                        AND str_to_date( CR.createdDate, '%m-%d-%Y' ) <= str_to_date('$today1', '%m-%d-%Y' ) and CP.proposaltype != 'ITB' and  CP.proposaltype != 'review' and CR.cust_id='".$report_manager_id."'";
                    $db->setQuery($v_recards_60days);
                    $final_manager_data['proposals_60days'] = $db->loadResult();

                    $v_recards_360days = "SELECT count(CP.id) as proposals_count FROM #__cam_rfpinfo as CR INNER JOIN #__cam_vendor_proposals as CP ON CR.id = CP.rfpno WHERE str_to_date( CR.createdDate, '%m-%d-%Y' ) > str_to_date( '$daysbefore_oneyear', '%m-%d-%Y' )
                        AND str_to_date( CR.createdDate, '%m-%d-%Y' ) <= str_to_date('$today1', '%m-%d-%Y' ) and CP.proposaltype != 'ITB' and  CP.proposaltype != 'review' and CR.cust_id='".$report_manager_id."'";
                    $db->setQuery($v_recards_360days);
                    $final_manager_data['proposals_365days'] = $db->loadResult();
		
                    $query_myven = "SELECT W.id from #__vendor_inviteinfo as U, #__cam_vendor_company as V, #__users as W where LOWER(U.inhousevendors) = (W.email) and V.user_id=W.id and W.block='0' AND U.exclude!='yes' and U.myvendors!='no' and U.search!='yes' and W.search='' and .U.userid='".$report_manager_id."' GROUP BY W.id";
                    $db->setQuery($query_myven);
                    $result_ven = $db->loadObjectList();
                    //echo '<pre>report_manager_id'; print_r($report_manager_id); echo '</pre>';
                    //echo '<pre>complianct'; print_r($result_ven); echo '</pre>';
                    unset($noncomplianct);
                    unset($complianct);
                    
                    for( $v=0; $v<count($result_ven); $v++ ){

                        $master   =	$this->getmasterfirm_owner($report_manager_id);
                        $vendor_industries = $this->getvendorindustries($result_ven[$v]->id);//get all vendor industries
			
			for( $in=0; $in<count($vendor_industries); $in++ ){
				//$master = $this->getmasterfirmaccount();
		
		
				$checkglobal	=	$this->checkglobalstandards($vendor_industries[$in]->industry_id,$master);
				
				if( $checkglobal == 'success' )	{
				$totalprefers_new_w9=$this->checknewspecialrequirements_w9_indus($result_ven[$v]->id,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_gli=$this->checknewspecialrequirements_gli_indus($result_ven[$v]->id,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_aip=$this->checknewspecialrequirements_aip_indus($result_ven[$v]->id,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_wci=$this->checknewspecialrequirements_wci_indus($result_ven[$v]->id,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_umb=$this->checknewspecialrequirements_umb_indus($result_ven[$v]->id,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_pln=$this->checknewspecialrequirements_pln_indus($result_ven[$v]->id,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_occ=$this->checknewspecialrequirements_occ_indus($result_ven[$v]->id,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_omi=$this->checknewspecialrequirements_omi_indus($result_ven[$v]->id,$vendor_industries[$in]->industry_id,$master);
				//echo '<pre>vendor_industries'; print_r($totalprefers_new_omi); echo '</pre>'; 
					if($totalprefers_new_w9 == 'success' && $totalprefers_new_gli == 'success' && $totalprefers_new_aip == 'success' && $totalprefers_new_wci == 'success' && $totalprefers_new_umb == 'success' && $totalprefers_new_pln == 'success' && $totalprefers_new_occ == 'success'  && $totalprefers_new_omi == 'success' ){ 
						$cstatus = 'complianct';
					} else {
				
						$cstatus = 'noncomplianct';
					}
				}
			}
				
					$subscribe_type = "SELECT subscribe_type FROM #__users where id ='".$result_ven[$v]->id."'";
					$db->setQuery($subscribe_type);
					$subscribe_type = $db->loadResult();

					if( $subscribe_type == 'free' || $subscribe_type == '' ) {
						//$c_status = 'Non-Compliant'.'red';
						//$cstatus = 'noncomplianct';
						$noncomplianct[]='1';
					} else {	
						$terms_exist = $this->gettermsandconditions($master);
						if($terms_exist == '1'){				
						$db =& JFactory::getDBO();
						$sql = "SELECT accepted FROM #__cam_vendor_terms WHERE masterid=".$master." and vendorid=".$result_ven[$v]->id." ";
						$db->setQuery($sql);
						$terms = $db->loadResult();
						if($terms == '1' && $cstatus == 'complianct'){
							//$cstatus = 'complianct';
							$complianct[]='1';
						} else {
							//$cstatus = 'noncomplianct';
							$noncomplianct[]='1';
						}
					} else {
						if($cstatus == 'complianct'){
							$complianct[]='1';
						} else {
							$noncomplianct[]='1';
						}
					}
				}	

	}	
	$final_manager_data['myven_nonc'] = count($noncomplianct);
        $final_manager_data['myven_com'] = count($complianct);
	//echo '<pre>$final_manager_data'; print_r($final_manager_data); echo '</pre>';

	 $query_contacts = "SELECT distinct(vendor),contract_amount from #__contracts_vendors where closed!='1' and vendor!='outsidevendor' and manager='".$report_manager_id."' ";
	 $db->setQuery($query_contacts);
	 $result_ven_c = $db->loadObjectList();
	unset($complianct_c);
	unset($noncomplianct_c);
	$final_manager_data['contract_amount'] = '';
	$contract_amount='';
	//echo '<pre>$result_ven_c'; print_r($result_ven_c); echo '</pre>';
//exit;

for( $v=0; $v<count($result_ven_c); $v++ ){

	$master   =	$this->getmasterfirm_owner($report_manager_id);

			$vendor_industries = $this->getvendorindustries($result_ven_c[$v]->vendor);//get all vendor industries
			for( $in=0; $in<count($vendor_industries); $in++ ){

				$checkglobal	=	$this->checkglobalstandards($vendor_industries[$in]->industry_id,$master);
				
				if( $checkglobal == 'success' )	{
				$totalprefers_new_w9=$this->checknewspecialrequirements_w9_indus($result_ven_c[$v]->vendor,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_gli=$this->checknewspecialrequirements_gli_indus($result_ven_c[$v]->vendor,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_aip=$this->checknewspecialrequirements_aip_indus($result_ven_c[$v]->vendor,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_wci=$this->checknewspecialrequirements_wci_indus($result_ven_c[$v]->vendor,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_umb=$this->checknewspecialrequirements_umb_indus($result_ven_c[$v]->vendor,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_pln=$this->checknewspecialrequirements_pln_indus($result_ven_c[$v]->vendor,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_occ=$this->checknewspecialrequirements_occ_indus($result_ven_c[$v]->vendor,$vendor_industries[$in]->industry_id,$master);
				$totalprefers_new_omi=$this->checknewspecialrequirements_omi_indus($result_ven_c[$v]->vendor,$vendor_industries[$in]->industry_id,$master);
				//echo '<pre>vendor_industries'; print_r($totalprefers_new_omi); echo '</pre>'; 
					if($totalprefers_new_w9 == 'success' && $totalprefers_new_gli == 'success' && $totalprefers_new_aip == 'success' && $totalprefers_new_wci == 'success' && $totalprefers_new_umb == 'success' && $totalprefers_new_pln == 'success' && $totalprefers_new_occ == 'success'  && $totalprefers_new_omi == 'success' ){ 
						$cstatus = 'complianct';
					} else {
				
						$cstatus = 'noncomplianct';
					}
				}
			}
					
					$subscribe_type = "SELECT subscribe_type FROM #__users where id ='".$result_ven_c[$v]->vendor."'";
					$db->setQuery($subscribe_type);
					$subscribe_type = $db->loadResult();
					if( $subscribe_type == 'free' || $subscribe_type == '' ) {
						//$c_status = 'Non-Compliant'.'red';
						//$cstatus = 'noncomplianct';
						//echo 'anand'; 
						$noncomplianct_c[]='1';
					} else {	
						$terms_exist = $this->gettermsandconditions($master);
						if($terms_exist == '1'){				
						$db =& JFactory::getDBO();
						$sql = "SELECT accepted FROM #__cam_vendor_terms WHERE masterid=".$master." and vendorid=".$result_ven_c[$v]->vendor." ";
						$db->setQuery($sql);
						$terms = $db->loadResult();
						if($terms == '1' && $cstatus == 'complianct'){
							//$cstatus = 'complianct';
							$complianct_c[]='1';
						} else {
                                                        //echo 'anand'; 
							//$cstatus = 'noncomplianct';
							$noncomplianct_c[]='1';
						}
					} else {
						if($cstatus == 'complianct'){
							$complianct_c[]='1';
						} else {
							$noncomplianct_c[]='1';
						}
					}
				}	
					
				$contract_amount[] = $result_ven_c[$v]->contract_amount;
		}
		$final_manager_data['contract_amount'] = array_sum($contract_amount);
		$final_manager_data['myven_nonc_cont'] = count($noncomplianct_c);
		$final_manager_data['myven_com_cont'] = count($complianct_c);
		$final_manager_data_final[$report_manager_id]=$final_manager_data;
            }  //echo '<pre>'; print_r($final_manager_data_final); echo '</pre>'; exit;
		
		return $final_manager_data_final;
        }
        
	function checkglobalstandards($industryid,$master)	{
			$db =& JFactory::getDBO();
			$query_gli = "SELECT id,industry_id FROM `jos_cam_master_generalinsurance_standards` where masterid=".$master." and industry_id= ".$industryid." " ;
			$db->Setquery($query_gli);
			$data_gli = $db->loadObjectList();
			
			$query_aip = "SELECT id,industryid FROM `jos_cam_master_autoinsurance_standards` where masterid=".$master." and industryid= ".$industryid." " ;
			$db->Setquery($query_aip);
			$data_aip = $db->loadObjectList();
			$query_umb = "SELECT id,industryid FROM `jos_cam_master_umbrellainsurance_standards` where masterid=".$master." and industryid= ".$industryid." " ;
			$db->Setquery($query_umb);
			$data_umb = $db->loadObjectList();
			$query_wci = "SELECT id,industryid FROM `jos_cam_master_workers_standards` where masterid=".$master." and industryid= ".$industryid." " ;
			$db->Setquery($query_wci);
			$data_wci = $db->loadObjectList();
			$query_lic = "SELECT id,industryid FROM `jos_cam_master_licinsurance_standards` where masterid=".$master." and industryid= ".$industryid." " ;
			$db->Setquery($query_lic);
			$data_lic = $db->loadObjectList();
				if( $data_gli || $data_aip || $data_umb || $data_wci || $data_lic )	{
					$existance =  "success";
				}
				else{
				$existance =  "fail";
				}
			return $existance ;	
	}
        
        function checknewspecialrequirements_w9_indus($vendorid,$industryid,$managerid){
	
	 	$db = & JFactory::getDBO();
		$w9_data ="SELECT * from #__cam_vendor_compliance_w9docs  WHERE vendor_id='".$vendorid."' order by id DESC"; //validation to status of docs
		$db->Setquery($w9_data);
		$vendor_w9_data = $db->loadObjectList();
		$whers_cond = 'masterid='.$managerid.'';
		
		 $rfp_w9_data ="SELECT * from #__cam_master_w9_standards WHERE ".$whers_cond." and industry_id=".$industryid; 
		$db->Setquery( $rfp_w9_data );
		$rfp_w9_data = $db->loadObject();
		if(!$rfp_w9_data){
			 $rfp_w9_data ="SELECT * from #__cam_master_w9_standards WHERE ".$whers_cond." and industry_id='56'"; 
			$db->Setquery( $rfp_w9_data );
			$rfp_w9_data = $db->loadObject();
		}
		
		//$occur_w9 = '';
		
		$occur_w9 = '';
			if($rfp_w9_data){
					if( !$vendor_w9_data[0]->w9_upld_cert || $vendor_w9_data[0]->w9_status == '-1') {
						$occur_w9[] = 'no' ;
					}
					else{
						$occur_w9[] = 'yes' ;
					}
				} else {
					$occur_w9[] = 'yes' ;
				}
		
				
			if($occur_w9){
				if( in_array("yes", $occur_w9) ){
					$special_w9 = "success";
				} else	{
					$special_w9 = "fail";
				}
			}
				

				
		//$cabins_w9[] = '';
		return $special_w9 ;		
	 }
	 
	 
	 function checknewspecialrequirements_gli_indus($vendorid,$industryid,$managerid){
		$totalprefers_new_gli = '';
		$db = & JFactory::getDBO();
		$gli_data ="SELECT * from #__cam_vendor_liability_insurence  WHERE vendor_id=".$vendorid; //validation to status of docs
		$db->Setquery($gli_data);
		$vendor_gli_data = $db->loadObjectList();
		//Get RFP data
		$whers_cond = 'masterid='.$managerid.'';
		$rfp_gli_data ="SELECT * from #__cam_master_generalinsurance_standards WHERE ".$whers_cond." and industry_id=".$industryid; //validation to status of docs
		$db->Setquery($rfp_gli_data);
		$rfp_gli_data = $db->loadObject();
		
		if(!$rfp_gli_data){
			 $rfp_gli_data ="SELECT * from #__cam_master_generalinsurance_standards WHERE ".$whers_cond." and industry_id='56'"; 
			$db->Setquery( $rfp_gli_data );
			$rfp_gli_data = $db->loadObject();
		}
		//echo "<br />";
		//echo "<pre>"; print_r($vendor_gli_data); echo "</pre>";
		//echo "<pre>"; print_r($rfp_gli_data); echo "</pre>";
		
		$occur = '';
		for( $gl=0; $gl<count($vendor_gli_data); $gl++ ){
			if($rfp_gli_data->occur ==  'yes' ){ 
				if( $vendor_gli_data[$gl]->GLI_occur == 'occur'){
					$occur[] = 'yes' ;
				} else {
					$occur[] = 'no' ;
				}
			 } else {
				$occur[] = 'yes' ;
			}
		
			if($rfp_gli_data->each_occurrence >  '0'){
				if($rfp_gli_data->each_occurrence <= $vendor_gli_data[$gl]->GLI_policy_occurence){
					$each_occurrence[] = 'yes' ;
				} else {
					$each_occurrence[] = 'no' ;
				}
			 } else {
				$each_occurrence[] = 'yes' ;
			}

			if($rfp_gli_data->damage_retend > '0'){
				if($rfp_gli_data->damage_retend <= $vendor_gli_data[$gl]->GLI_damage){
					$damage_retend[] = 'yes' ;
				} else {
					$damage_retend[] = 'no' ;
				}
			} else {
				$damage_retend[] = 'yes' ;
			}

			if($rfp_gli_data->med_expenses > '0'){
				if($rfp_gli_data->med_expenses <= $vendor_gli_data[$gl]->GLI_med){
					$med_expenses[] = 'yes' ;
				} else {
					$med_expenses[] = 'no' ;
				}
			} else {
				$med_expenses[] = 'yes' ;
			}	 
			if($rfp_gli_data->personal_inj > '0'){
				if($rfp_gli_data->personal_inj <= $vendor_gli_data[$gl]->GLI_injury){
					$personal_inj[] = 'yes' ;
				} else {
					$personal_inj[] = 'no' ;
				}
			} else {
				$personal_inj[] = 'yes' ;
			}	 
			if($rfp_gli_data->general_aggr > '0'){	
				if($rfp_gli_data->general_aggr <= $vendor_gli_data[$gl]->GLI_policy_aggregate){
					$general_aggr[] = 'yes' ;
				} else {
					$general_aggr[] = 'no' ;
				}
			} else {
				$general_aggr[] = 'yes' ;
			}	  
			$total_applies= explode(',',$rfp_gli_data->applies_to);
			if($total_applies!='' && $rfp_gli_data->applies_to!=''){
				$vendor_applies= explode(',',$vendor_gli_data[$gl]->GLI_applies);
				if( array_intersect($vendor_applies, $total_applies) ){
					$applies_to[] = 'yes' ;
				} else{
					$applies_to[] = 'no' ;
				}
			} else {
				$applies_to[] = 'yes' ;
			}	 
			
			if($rfp_gli_data->products_aggr >  '0'){
				if($rfp_gli_data->products_aggr <= $vendor_gli_data[$gl]->GLI_products){
					$products_aggr[] = 'yes' ;
				} else {
					$products_aggr[] = 'no' ;
				}
			} else {
				$products_aggr[] = 'yes' ;
			}		 
			if($rfp_gli_data->waiver_sub == 'yes') {
				if($vendor_gli_data[$gl]->GLI_waiver == 'waiver'){
					$waiver_sub[] = 'yes' ;
				} else {
					$waiver_sub[] = 'no' ;
				}
			} else {
				$waiver_sub[] = 'yes' ;
			}	 
			if($rfp_gli_data->primary_noncontr == 'yes') {
				if($vendor_gli_data[$gl]->GLI_primary == 'primary'){
					$primary_noncontr[] = 'yes' ;
				} else {
					$primary_noncontr[] = 'no' ;
				}
			} else {
				$primary_noncontr[] = 'yes' ;
			}	  
				
			if($rfp_gli_data->additional_insured == 'yes') {
				if($vendor_gli_data[$gl]->GLI_additional && $vendor_gli_data[$gl]->GLI_additional==$rfp_gli_data->masterid){
					$additional_insured[] = 'yes' ;
				} else {
					$additional_insured[] = 'no' ;
				}
			} else {
				$additional_insured[] = 'yes' ;
			}	   
			if($rfp_gli_data->cert_holder == 'yes') {
				if($vendor_gli_data[$gl]->GLI_certholder == 'yes'){
					$cert_holder[] = 'yes' ;
				} else {
					$cert_holder[] = 'no' ;
				}
			} else {
				$cert_holder[] = 'yes' ;
			}	  
				if($rfp_gli_data){
					if($vendor_gli_data[$gl]->GLI_end_date < date('Y-m-d') || !$vendor_gli_data[$gl]->GLI_upld_cert || !$vendor_gli_data[$gl]->GLI_policy_occurence || !$vendor_gli_data[$gl]->GLI_policy_aggregate || $vendor_gli_data[$gl]->GLI_status == '-1') {
						$GLI_end_date[] = 'no' ;
					} else {
						$GLI_end_date[] = 'yes' ;
					}
				} else {
					$GLI_end_date[] = 'yes' ;
				}	 
			 	if($rfp_gli_data->require_language == 'yes' || $rfp_gli_data->require_text != '') {
			if($vendor_gli_data[$gl]->GLI_language == 'yes' && $vendor_gli_data[$gl]->GLI_additional==$rfp_gli_data->masterid){
					$require_language[] = 'yes' ;
				} else {
					$require_language[] = 'no' ;
				}
			} else {
				$require_language[] = 'yes' ;
			}	 

		}
		
		/* if($cabins_gli){
			if( in_array("yes", $cabins_gli) ){
			$special = "success";
			}
			else{
			$special = "fail";
			}
			
		}
		else{
				if($rfp_gli_data)
				$special = "fail";
				else
				$special = "success";
		}
			
		$cabins_gli = '';*/

		if( in_array("yes", $occur) && in_array("yes", $each_occurrence) && in_array("yes", $damage_retend) && in_array("yes", $med_expenses) && in_array("yes", $personal_inj) && in_array("yes", $general_aggr) && in_array("yes", $applies_to) && in_array("yes", $products_aggr) && in_array("yes", $waiver_sub) && in_array("yes", $primary_noncontr) && in_array("yes", $additional_insured) && in_array("yes", $cert_holder) && in_array("yes", $GLI_end_date) && in_array("yes", $require_language)){ 
				$special = "success";
			} else {
				$special = "fail";
			}
		
		return $special ;
		
	}
//Completed
	
	function checknewspecialrequirements_aip_indus($vendorid,$industryid,$managerid){
		$db = & JFactory::getDBO();
		$aip_data ="SELECT * from #__cam_vendor_auto_insurance  WHERE vendor_id=".$vendorid; //validation to status of docs
		$db->Setquery($aip_data);
		$vendor_aip_data = $db->loadObjectList();
		//Get RFP data
		$whers_cond = 'masterid='.$managerid.'';
		$rfp_aip_data ="SELECT * from #__cam_master_autoinsurance_standards WHERE ".$whers_cond." and industryid=".$industryid; //validation to status of docs
		$db->Setquery($rfp_aip_data);
		$rfp_aip_data = $db->loadObject();
		
		if(!$rfp_aip_data){
			$rfp_aip_data ="SELECT * from #__cam_master_autoinsurance_standards WHERE ".$whers_cond." and industryid='56'"; //validation to status of docs
			$db->Setquery($rfp_aip_data);
			$rfp_aip_data = $db->loadObject();
		}
		
		
			for( $ai=0; $ai<count($vendor_aip_data); $ai++ ){
				if($rfp_aip_data->applies_to_any == 'any' ){ 
					if($rfp_aip_data->applies_to_any == $vendor_aip_data[$ai]->aip_applies_any){
						$occur_aip[] = 'yes' ;
					} else {
						$occur_aip[] = 'no' ;
					}
				 } else {
						$occur_aip[] = 'yes' ;
				 }
				

				 if($rfp_aip_data->applies_to_owned == 'owned'){
					if($rfp_aip_data->applies_to_owned == $vendor_aip_data[$ai]->aip_applies_owned){
						$applies_to_owned_aip[] = 'yes' ;
					} else {
						$applies_to_owned_aip[] = 'no' ;
					}
				 } else {
						$applies_to_owned_aip[] = 'yes' ;
				 }
 
				if($rfp_aip_data->applies_to_nonowned == 'nonowned'){
					if($rfp_aip_data->applies_to_nonowned == $vendor_aip_data[$ai]->aip_applies_nonowned){
						$applies_to_nonowned_aip[] = 'yes' ;
					} else {
						$applies_to_nonowned_aip[] = 'no' ;
					}
				} else {
						$applies_to_nonowned_aip[] = 'yes' ;
				}
 
				if($rfp_aip_data->applies_to_hired == 'hired'){
					if($rfp_aip_data->applies_to_hired == $vendor_aip_data[$ai]->aip_applies_hired){
						$applies_to_hired_aip[] = 'yes' ;
					} else {
						$applies_to_hired_aip[] = 'no' ;
					}
				} else {
						$applies_to_hired_aip[] = 'yes' ;
				}

				if($rfp_aip_data->applies_to_scheduled == 'scheduled'){
					if($vendor_aip_data[$ai]->aip_applies_scheduled == 'sch'){
						$applies_to_scheduled_aip[] = 'yes' ;
					} else {
						$applies_to_scheduled_aip[] = 'no' ;
					}
				} else {
						$applies_to_scheduled_aip[] = 'yes' ;
				}
				
				
				if($rfp_aip_data->combined_single > '0'){	
					if($rfp_aip_data->combined_single <= $vendor_aip_data[$ai]->aip_combined){
						$combined_single_aip[] = 'yes' ;
					} else {
						$combined_single_aip[] = 'no' ;
					}
				} else {
						$combined_single_aip[] = 'yes' ;
				}
				
				
				if($rfp_aip_data->bodily_injusy_person > '0'){	
					if($rfp_aip_data->bodily_injusy_person <= $vendor_aip_data[$ai]->aip_bodily){
						$bodily_injusy_person_aip[] = 'yes' ;
					} else {
						$bodily_injusy_person_aip[] = 'no' ;
					}
				} else {
						$bodily_injusy_person_aip[] = 'yes' ;
				}
				
				if($rfp_aip_data->bodily_injusy_accident > '0'){	
					if($rfp_aip_data->bodily_injusy_accident <= $vendor_aip_data[$ai]->aip_body_injury){
						$bodily_injusy_accident_aip[] = 'yes' ;
					} else {
						$bodily_injusy_accident_aip[] = 'no' ;
					}
				} else {
						$bodily_injusy_accident_aip[] = 'yes' ;
				}
				
				
				if($rfp_aip_data->property_damage > '0'){	
					if($rfp_aip_data->property_damage <= $vendor_aip_data[$ai]->aip_property){
						$property_damage_aip[] = 'yes' ;
					} else {
						$property_damage_aip[] = 'no' ;
					}
				} else {
						$property_damage_aip[] = 'yes' ;
				}
			
				
				if($rfp_aip_data->waiver == 'yes'){
					if($vendor_aip_data[$ai]->aip_waiver == 'waiver'){
						$waiver_aip[] = 'yes' ;
					} else {
						$waiver_aip[] = 'no' ;
					}
				} else {
						$waiver_aip[] = 'yes' ;
				}
					
				
				if($rfp_aip_data->primary == 'yes'){
					if($vendor_aip_data[$ai]->aip_primary == 'primary'){
						$primary_aip[] = 'yes' ;
					} else {
						$primary_aip[] = 'no' ;
					}
				} else {
						$primary_aip[] = 'yes' ;
				}
				
				if($rfp_aip_data->additional_ins == 'yes'){
					if($vendor_aip_data[$ai]->aip_addition == '' || $vendor_aip_data[$ai]->aip_addition == '0' || $vendor_aip_data[$ai]->aip_addition!=$rfp_aip_data->masterid){
						$additional_ins_aip[] = 'no' ;
					} else {
						$additional_ins_aip[] = 'yes' ;
					}
				} else {
						$additional_ins_aip[] = 'yes' ;
				}
				
				
				if($rfp_aip_data->cert_holder == 'yes'){
					if($vendor_aip_data[$ai]->aip_cert == 'yes'){
						$cert_holder_aip[] = 'yes' ;
					} else {
						$cert_holder_aip[] = 'no' ;
					}
				} else {
						$cert_holder_aip[] = 'yes' ;
				}
				
				
				if($rfp_aip_data){
					if($vendor_aip_data[$ai]->aip_end_date < date('Y-m-d') || $vendor_aip_data[$ai]->aip_upld_cert=='' || $vendor_aip_data[$ai]->aip_status == '-1' ){
						$aip_end_date_aip[] = 'no' ;
					} else	{
						$aip_end_date_aip[] = 'yes' ;
					}
				} else {
						$aip_end_date_aip[] = 'yes' ;
				}
				
				if($rfp_aip_data->require_autolanguage ==  'yes' ){
					if( $vendor_aip_data[$ai]->aip_language == 'yes' && $vendor_aip_data[$ai]->aip_addition==$rfp_aip_data->masterid){
						$require_autolanguage_aip[] = 'yes' ;
					} else {
						$require_autolanguage_aip[] = 'no' ;
					}
				} else {
						$require_autolanguage_aip[] = 'yes' ;
				}	
			}	
			/* if($cabins_aip){
				if( in_array("yes", $cabins_aip) ){
					$special_aip = "success";
				}
				else{
					$special_aip = "fail";
				}
			}
			else{
				if($rfp_aip_data)
				$special_aip = "fail";
				else
				$special_aip = "success";
			}
			
				$cabins_aip = '';
		*/
		if( in_array("yes", $occur_aip) && in_array("yes", $applies_to_owned_aip) && in_array("yes", $applies_to_nonowned_aip) && in_array("yes", $applies_to_hired_aip) && in_array("yes", $applies_to_scheduled_aip) && in_array("yes", $combined_single_aip) && in_array("yes", $bodily_injusy_person_aip) && in_array("yes", $bodily_injusy_accident_aip) && in_array("yes", $property_damage_aip) && in_array("yes", $waiver_aip) && in_array("yes", $primary_aip) && in_array("yes", $additional_ins_aip) && in_array("yes", $cert_holder_aip) && in_array("yes", $aip_end_date_aip) && in_array("yes", $require_autolanguage_aip)){
				$special_aip = "success";
			} else {
				$special_aip = "fail";
			}
		
		return $special_aip ;
		
		
	}
	
		//Function to check WCI documents
	function checknewspecialrequirements_wci_indus($vendorid,$industryid,$managerid){
		
		$db = & JFactory::getDBO();
		$wci_data ="SELECT * from #__cam_vendor_workers_companies_insurance  WHERE vendor_id=".$vendorid; //validation to status of docs
		$db->Setquery($wci_data);
		$vendor_wci_data = $db->loadObjectList();
		//Get RFP data
		$whers_cond = 'masterid='.$managerid.'';
		$rfp_wci_data ="SELECT * from #__cam_master_workers_standards WHERE ".$whers_cond." and industryid=".$industryid; //validation to status of docs
		$db->Setquery($rfp_wci_data);
		$rfp_wci_data = $db->loadObject();

		if(!$rfp_wci_data){
			$rfp_wci_data ="SELECT * from #__cam_master_workers_standards WHERE ".$whers_cond." and industryid='56'"; //validation to status of docs
			$db->Setquery($rfp_wci_data);
			$rfp_wci_data = $db->loadObject();
		}

			for( $wci=0; $wci<count($vendor_wci_data); $wci++ ){
				
				if($rfp_wci_data->disease_policy > '0'){	
					if($rfp_wci_data->disease_policy <= $vendor_wci_data[$wci]->WCI_disease_policy){
						$disease_policy_wci[] = 'yes' ;
					} else {
						$disease_policy_wci[] = 'no' ;
					}
				 } else {
					 $disease_policy_wci[] = 'yes' ;
				 }
					
				if($rfp_wci_data->disease_eachemp > '0'){
					if($rfp_wci_data->disease_eachemp <= $vendor_wci_data[$wci]->WCI_disease){
						$disease_eachemp_wci[] = 'yes' ;
					} else {
						$disease_eachemp_wci[] = 'no' ;
					}
				 } else {
					 $disease_eachemp_wci[] = 'yes' ;
				 }
				
				if($rfp_wci_data->waiver_work == 'yes'){
					if($vendor_wci_data[$wci]->WCI_waiver == 'waiver'){
						$waiver_work_wci[] = 'yes' ;
					} else {
						$waiver_work_wci[] = 'no' ;
					}
				} else {
					 $waiver_work_wci[] = 'yes' ;
				 }
				
				if($rfp_wci_data->each_accident > '0'){
					if($rfp_wci_data->each_accident <= $vendor_wci_data[$wci]->WCI_each_accident){
						$each_accident_wci[] = 'yes' ;
					} else {
						$each_accident_wci[] = 'no' ;
					}
				} else {
					 $each_accident_wci[] = 'yes' ;
				 }
				
				if($rfp_wci_data->certholder_work == 'yes'){
					if($vendor_wci_data[$wci]->WCI_cert == 'yes'){
						$certholder_work_wci[] = 'yes' ;
					} else {
						$certholder_work_wci[] = 'no' ;
					}
				} else {
					 $certholder_work_wci[] = 'yes' ;
				 }
				if($rfp_wci_data){
					if($vendor_wci_data[$wci]->WCI_end_date < date('Y-m-d') || $vendor_wci_data[$wci]->WCI_upld_cert=='' || $vendor_wci_data[$wci]->WCI_status == '-1') {
						$WCI_end_date_wci[] = 'no' ;
					} else {
						$WCI_end_date_wci[] = 'yes' ;
					}
				} else {
					 $WCI_end_date_wci[] = 'yes' ;
				 }	
			}
			
		/*	if($cabins_wci){
				if( in_array("yes", $cabins_wci) ){
					$special_wci = "success";
				}
				else{
					$special_wci = "fail";
				}
			}
			else{
				if($rfp_wci_data)
				$special_wci = "fail";
				else
				$special_wci = "success";
			}
			
				$cabins_wci = ''; */
			if( in_array("yes", $disease_policy_wci) && in_array("yes", $disease_eachemp_wci) && in_array("yes", $waiver_work_wci) && in_array("yes", $each_accident_wci) && in_array("yes", $certholder_work_wci) && in_array("yes", $WCI_end_date_wci)){
				$special_wci = "success";
			} else {
				$special_wci = "fail";
			}
				
		
		return $special_wci ;
	}
	
	//COmpleted
	
	//function to check umbrella liability documents
	 function checknewspecialrequirements_umb_indus($vendorid,$industryid,$managerid){
		$db = & JFactory::getDBO();
		$umb_data ="SELECT * from #__cam_vendor_umbrella_license  WHERE vendor_id=".$vendorid; //validation to status of docs
		$db->Setquery($umb_data);
		$vendor_umb_data = $db->loadObjectList();
		//Get RFP data
		$whers_cond = 'masterid='.$managerid.'';
		$rfp_umb_data ="SELECT * from #__cam_master_umbrellainsurance_standards WHERE ".$whers_cond." and industryid=".$industryid; //validation to status of docs
		$db->Setquery($rfp_umb_data);
		$rfp_umb_data = $db->loadObject();
		
		if(!$rfp_umb_data){
			$rfp_umb_data ="SELECT * from #__cam_master_umbrellainsurance_standards WHERE ".$whers_cond." and industryid='56'"; //validation to status of docs
			$db->Setquery($rfp_umb_data);
			$rfp_umb_data = $db->loadObject();
		}
		
			for( $umb=0; $umb<count($vendor_umb_data); $umb++ ){
				
				if($rfp_umb_data->each_occur > '0'){
					if($rfp_umb_data->each_occur <= $vendor_umb_data[$umb]->UMB_occur ){
						$each_occur_umb[] = 'yes' ;
					} else {
						$each_occur_umb[] = 'no' ;
					}
				} else {
					$each_occur_umb[] = 'yes' ;
				}
				
				if($rfp_umb_data->aggregate > '0'){	
					if($rfp_umb_data->aggregate <= $vendor_umb_data[$umb]->UMB_aggregate){
						$aggregate_umb[] = 'yes' ;
					} else {
						$aggregate_umb[] = 'no' ;
					}
				} else {
					$aggregate_umb[] = 'yes' ;
				}	
				if($rfp_umb_data->certholder_umbrella == 'yes'){
					if($vendor_umb_data[$umb]->UMB_certholder == 'yes'){
						$certholder_umbrella_umb[] = 'yes' ;
					} else {
						$certholder_umbrella_umb[] = 'no' ;
					}
				} else {
					$certholder_umbrella_umb[] = 'yes' ;
				}	
				
				
				if($rfp_umb_data->require_umblanguage ==  'yes' ){
				if( $vendor_umb_data[$umb]->UMB_language == 'yes' && $vendor_umb_data[$umb]->UMB_addition==$rfp_umb_data->masterid){
					$umbrella_insured_umb[] = 'yes' ;
				} else {
					$umbrella_insured_umb[] = 'no' ;
				}
			} else {
					$umbrella_insured_umb[] = 'yes' ;
				}	
			
			if($rfp_umb_data->umbrella_insured == 'yes') {
				if($vendor_umb_data[$umb]->UMB_addition && $vendor_umb_data[$umb]->UMB_addition==$rfp_umb_data->masterid){
					$UMB_addition_umb[] = 'yes' ;
				} else {
					$UMB_addition_umb[] = 'no' ;
				}
			} else {
					$UMB_addition_umb[] = 'yes' ;
				}	
			if($rfp_umb_data){
				if($vendor_umb_data[$umb]->UMB_expdate < date('Y-m-d') || !$vendor_umb_data[$umb]->UMB_upld_cert || $vendor_umb_data[$umb]->UMB_status == '-1' || !$vendor_umb_data[$umb]->UMB_aggregate || !$vendor_umb_data[$umb]->UMB_occur) {
					$UMB_expdate_umb[] = 'no' ;
				} else {
					$UMB_expdate_umb[] = 'yes' ;
				}
			} else {
				$UMB_expdate_umb[] = 'yes' ;
			}	
			
			}	 
				
			/*	if($cabins_umb){
					if( in_array("yes", $cabins_umb) ){
						$special_umb = "success";
					}
					else{
						$special_umb = "fail";
					}
				}
				else{
					if($rfp_umb_data)
					$special_umb = "fail";
					else
					$special_umb = "success";
				}
		
				$cabins_umb = '';*/
			if( in_array("yes", $each_occur_umb) && in_array("yes", $aggregate_umb) && in_array("yes", $certholder_umbrella_umb) && in_array("yes", $umbrella_insured_umb) && in_array("yes", $UMB_addition_umb) && in_array("yes", $UMB_expdate_umb)){
				$special_umb = "success";
			} else {
				$special_umb = "fail";
			}
				return $special_umb ;
	 }
	//Completed
	
	//Funcion to check professional licensw
	function checknewspecialrequirements_pln_indus($vendorid,$industryid,$managerid){

		$db = & JFactory::getDBO();
		$pln_data ="SELECT * from #__cam_vendor_professional_license  WHERE vendor_id=".$vendorid; //validation to status of docs
		$db->Setquery($pln_data);
		$vendor_pln_data = $db->loadObjectList();
		//Get RFP data
		$whers_cond = 'masterid='.$managerid.'';
		$rfp_pln_data ="SELECT * from #__cam_master_licinsurance_standards WHERE ".$whers_cond." and industryid=".$industryid; //validation to status of docs
		$db->Setquery($rfp_pln_data);
		$rfp_pln_data = $db->loadObject();
		if(!$rfp_pln_data){
			$rfp_pln_data ="SELECT * from #__cam_master_licinsurance_standards WHERE ".$whers_cond." and industryid='56'"; //validation to status of docs
			$db->Setquery($rfp_pln_data);
			$rfp_pln_data = $db->loadObject();
		}
		
			for( $pln=0; $pln<count($vendor_pln_data); $pln++ ){
					if($rfp_pln_data->professional == 'yes'){
					if($vendor_pln_data[$pln]->PLN_expdate < date('Y-m-d') || !$vendor_pln_data[$pln]->PLN_upld_cert || $vendor_pln_data[$pln]->PLN_status == '-1') {
						$PLN_expdate_pln[] = 'no' ;
					} else {
						$PLN_expdate_pln[] = 'yes' ;
					}
				} else {
					$PLN_expdate_pln[] = 'yes' ;
				}
			}	
			
		/*	if($cabins_pln){
				if( in_array("yes", $cabins_pln) ){
					$special_pln = "success";
				}
				else{
					$special_pln = "fail";
				}
				$cabins_pln = '';
			}
			
			else{
					if($rfp_pln_data->professional)
					$special_pln = "fail";
					else
					$special_pln = "success";
			}
			
				$cabins_pln = '';*/

				if( in_array("yes", $PLN_expdate_pln) ){
					$special_pln = "success";
				} else {
					$special_pln = "fail";
				}
				return $special_pln ;
	}
	//Completed	
	
	function checknewspecialrequirements_occ_indus($vendorid,$industryid,$managerid){

		$db = & JFactory::getDBO();
		$occ_data ="SELECT * from #__cam_vendor_occupational_license  WHERE vendor_id=".$vendorid; //validation to status of docs
		$db->Setquery($occ_data);
		$vendor_occ_data = $db->loadObjectList();
		//Get RFP data
		$whers_cond = 'masterid='.$managerid.'';
		$rfp_occ_data ="SELECT * from #__cam_master_licinsurance_standards WHERE ".$whers_cond." and industryid=".$industryid; //validation to status of docs
		$db->Setquery($rfp_occ_data);
		$rfp_occ_data = $db->loadObject();
		if(!$rfp_occ_data){
			$rfp_occ_data ="SELECT * from #__cam_master_licinsurance_standards WHERE ".$whers_cond." and industryid='56'"; //validation to status of docs
			$db->Setquery($rfp_occ_data);
			$rfp_occ_data = $db->loadObject();
		}
		
			for( $occ=0; $occ<count($vendor_occ_data); $occ++ ){
			
					if($rfp_occ_data->occupational == 'yes'){
					if($vendor_occ_data[$occ]->OLN_expdate < date('Y-m-d') || !$vendor_occ_data[$occ]->OLN_upld_cert || $vendor_occ_data[$pln]->OLN_status == '-1') {
						$OLN_expdate_occ[] = 'no' ;
					} else {
						$OLN_expdate_occ[] = 'yes' ;
					}
				} else {
					$OLN_expdate_occ[] = 'yes' ;
				}
			}	
			
			/* if($cabins_occ){
				if( in_array("yes", $cabins_occ) ){
					$special_occ = "success";
				}
				else{
					$special_occ = "fail";
				}
				$cabins_occ = '';
			}
			
			else{
					if($rfp_occ_data->occupational)
					$special_occ = "fail";
					else
					$special_occ = "success";
			}
			
				$cabins_occ = ''; */
			if( in_array("yes", $OLN_expdate_occ) ){
				$special_occ = "success";
			} else {
				$special_occ = "fail";
			}
				return $special_occ ;
	}
	//Completed
	
	function checknewspecialrequirements_omi_indus($vendorid,$industryid,$managerid){

		$db = & JFactory::getDBO();
		$omi_data ="SELECT * from #__cam_vendor_errors_omissions_insurance  WHERE vendor_id=".$vendorid; //validation to status of docs
		$db->Setquery($omi_data);
		$vendor_omi_data = $db->loadObjectList();
		//Get RFP data
		$rfp_omi_data ="SELECT * from #__cam_master_errors_omissions WHERE masterid=".$managerid." and industryid=".$industryid; //validation to status of docs
		$db->Setquery($rfp_omi_data);
		$rfp_omi_data = $db->loadObject();
		
		if(!$rfp_omi_data){
			$rfp_omi_data ="SELECT * from #__cam_master_errors_omissions WHERE masterid=".$managerid." and industryid='56'"; //validation to status of docs
			$db->Setquery($rfp_omi_data);
			$rfp_omi_data = $db->loadObject();
		}
		
		
		
			for( $omi=0; $omi<count($vendor_omi_data); $omi++ ){
				 if($rfp_omi_data->each_claim > '0' ){	
					if($rfp_omi_data->each_claim <= $vendor_omi_data[$omi]->OMI_each_claim ){
						$each_claim_omi[] = 'yes' ;
					} else {
						$each_claim_omi[] = 'no' ;
					}
				} else {
					$each_claim_omi[] = 'yes' ;
				} 
			if($rfp_omi_data->aggregate_omi > '0'){	
					if($rfp_omi_data->aggregate_omi <= $vendor_omi_data[$omi]->OMI_aggregate){
						$aggregate_omi_omi[] = 'yes' ;
					} else {
						$aggregate_omi_omi[] = 'no' ;
					}
				} else {
					$aggregate_omi_omi[] = 'yes' ;
				} 	
				if($rfp_omi_data->certholder_omi == 'yes'){
					if($vendor_omi_data[$omi]->OMI_cert == 'yes'){
						$certholder_omi_omi[] = 'yes' ;
					} else {
						$certholder_omi_omi[] = 'no' ;
					}
				} else {
					$certholder_omi_omi[] = 'yes' ;
				} 	
				
				
				if($rfp_omi_data->require_omilanguage ==  'yes' ){
				if( $vendor_omi_data[$omi]->OMI_language == 'yes' && $vendor_omi_data[$omi]->OMI_additional==$rfp_omi_data->masterid ){
					$omi_insured_omi[] = 'yes' ;
				} else {
					$omi_insured_omi[] = 'no' ;
				}
			} else {
					$omi_insured_omi[] = 'yes' ;
				} 	
			
			if($rfp_omi_data->omi_insured == 'yes') {
				if($vendor_omi_data[$omi]->OMI_additional && $vendor_omi_data[$omi]->OMI_additional==$rfp_omi_data->masterid){
					$OMI_additional_omi[] = 'yes' ;
				} else {
					$OMI_additional_omi[] = 'no' ;
				}
			} else {
					$OMI_additional_omi[] = 'yes' ;
				} 	
			if($rfp_omi_data){
				if($vendor_omi_data[$omi]->OMI_end_date < date('Y-m-d') || !$vendor_omi_data[$omi]->OMI_upld_cert || $vendor_omi_data[$omi]->OMI_status == '-1' ) {
					$OMI_end_date_omi[] = 'no' ;
				} else {
					$OMI_end_date_omi[] = 'yes' ;
				}
			} else {
					$OMI_end_date_omi[] = 'yes' ;
				} 	
			
			}	
			
			/*	if($cabins_omi){
					if( in_array("yes", $cabins_omi) ){
						$special_omi = "success";
					}
					else{
						$special_omi = "fail";
					}
				}
				else{
					if($rfp_omi_data)
					$special_omi = "fail";
					else
					$special_omi = "success";
				}
		
				$cabins_omi = '';*/
				if( in_array("yes", $each_claim_omi) && in_array("yes", $aggregate_omi_omi) && in_array("yes", $certholder_omi_omi) && in_array("yes", $omi_insured_omi) && in_array("yes", $OMI_additional_omi) && in_array("yes", $OMI_end_date_omi)){
					$special_omi = "success";
				} else {
					$special_omi = "fail";
				}
				return $special_omi ;
	}
	//Completed
        
        //Function to get master terms and conditions
	function gettermsandconditions($masterid){
		$db =& JFactory::getDBO();
		$sql_terms = "SELECT termsconditions FROM #__cam_vendor_aboutus WHERE vendorid=".$masterid." "; 
		$db->setQuery($sql_terms);
		$terms_exist = $db->loadResult();
		return $terms_exist;
	}
        
        function reportmessage()
	{
		$db	= JFactory::getDBO();
		//$query = "SELECT introtext  FROM #__content where id='356'";//local
		$query = "SELECT introtext  FROM #__content where id='361'"; //LIVE 361 
                
		$db->setQuery( $query );
		$body = $db->loadResult();
		return $body; 
	}
        
        /* Function For Manager Information Start */         
        public function getUserIdInfo($manager_id = '')
        {
            $db = & JFactory::getDBO();
            $sql_userinfo_qry = "SELECT id, user_type, dmanager, accounttype from jos_users where `id` = '".$manager_id."'";
            $db->Setquery($sql_userinfo_qry);
            $vendor_userinfo_data = $db->loadObject();
            return $vendor_userinfo_data;
        }
        /* Function For Manager Information End */  
        
        /* Function For Manager Compnay Name Information Start */  
        public function getManagerCompnayNameInfo($manager_id = '')
        {
            $db = & JFactory::getDBO();
            $comp_info_name_qry = "SELECT comp_name from `jos_cam_customer_companyinfo` where `cust_id` = '$manager_id'";
            $db->setQuery($comp_info_name_qry);
            $comp_info_name = $db->loadResult();
            return $comp_info_name;
        }
        /* Function For Manager Compnay Name Information End */  
        
        public function updateNotificationMailLogInfo($report_id = '', $to_email_report = '')
        {
            $db = & JFactory::getDBO();
            $today = date('Y-m-d H:i:s');
            
            $rfpapproval = "SELECT id FROM jos_compliance_notifications where comp_report_id='".$report_id."' and email_id='".$to_email_report."' ";
            $db->Setquery($rfpapproval);
            $rfpapprova = $db->loadResult();

            if($rfpapprova){
                    $updatereq = "UPDATE jos_compliance_notifications SET email_date = '".$today."' where id = ".$rfpapprova."";
                    $db->setQuery($updatereq);
                   // $db->query();
            }
            else 
            {
                    $sql2 = "insert into jos_compliance_notifications values ('','".$report_id."','".$to_email_report."','".$today."')"; 
                    $db->SetQuery($sql2);
                    //$db->query();
            }
            return true;
        }
        
        public function updateReportMailLastSendDate($report_id = '')
        {
            $db = & JFactory::getDBO();
            $today = date('Y-m-d H:i:s');
            
            $updatereq = "UPDATE jos_cam_master_email_compliance_status SET date = '".$today."' where comp_report_id = ".$report_id."";
            echo 'Final update qry is '.$updatereq;
            $db->setQuery($updatereq);
            //$db->query();
            
            return true;
        }
    }
?>