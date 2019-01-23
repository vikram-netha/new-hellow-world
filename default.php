<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>camassistant</title>
<?php  //echo '<pre>'; print_r($_REQUEST);
error_reporting(0);
	$managertype = JRequest::getVar( 'managertype',''); 
	$industrytype = JRequest::getVar( 'industrytype',''); 
	$compliance_filter = JRequest::getVar( 'compliance',''); 
	$statelist = $this->statelist; 
	$ownids = $this->own; 
	$globe = $this->global; 
	$user=& JFactory::getuser();
	$basicjobs = $this->basisjobs ;
	if( count($basicjobs) > '0' )
	$basics = 'yes';
	else
	$basics = 'no';
	$permission = $this->permission ;
	
	$recommends = $this->recommends ;
	$countofmngrs = count($this->managers_recs);
	//echo $countofmngrs; 
	if( $countofmngrs == 0 )
		$height = '300';
	else if( $countofmngrs > 0 && $countofmngrs <= 6 )
		$height = '350';
	else
		$height = '370';
		

?>
<style>
#maskvrec {  position:absolute;  left:0;  top:0;  z-index:9000;  background-color:#000;  display:none;}
#boxesvrec .windowvrec {  position:absolute;  left:0;  top:0;  width:1300px;  height:150px;  display:none;  z-index:9999;  padding:38px 10px 3px 10px;}
#boxesvrec #submitvrec {  width:318px;  height:117px;  padding:10px;  background-color:#ffffff;}
#boxesvrec #submitvrec a{ text-decoration:none; color:#000000; font-weight:bold; font-size:20px;}
#donevrec {border:0 none; cursor:pointer; height:30px; margin-left:-17px; margin-top:-29px; width:474px; float:left; }
#closevrec { border:0 none; cursor:pointer; height:30px; color:#000000; font-weight:bold; font-size:20px; text-align:center;}

#maskv {  position:absolute;  left:0;  top:0;  z-index:9000;  background-color:#000;  display:none;}
#boxesv .windowv {  position:absolute;  left:0;  top:0;  width:1300px;  height:150px;  display:none;  z-index:9999;  padding:38px 10px 3px 10px;}
#boxesv #submitv {  width:318px;  height:117px;  padding:10px;  background-color:#ffffff;}
#boxesv #submitv a{ text-decoration:none; color:#000000; font-weight:bold; font-size:20px;}
#donev {border:0 none; cursor:pointer; height:30px; margin-left:-17px; margin-top:-29px; width:474px; float:left; }
#closev { border:0 none; cursor:pointer; height:30px; margin:0 0 0 8px; color:#000000; font-weight:bold; font-size:20px; width:172px;}

#maskun {  position:absolute;  left:0;  top:0;  z-index:9000;  background-color:#000;  display:none;}
#boxesun .windowun {  position:absolute;  left:0;  top:0;  width:1300px;  height:150px;  display:none;  z-index:9999;  padding:38px 10px 3px 10px;}
#boxesun #submitun {  width:318px;  height:117px;  padding:10px;  background-color:#ffffff;}
#boxesun #submitun a{ text-decoration:none; color:#000000; font-weight:bold; font-size:20px;}
#doneun {border:0 none; cursor:pointer; height:30px; margin-left:-78px; margin-top:-11px; width:474px; float:left; }
#closeun { border:0 none; cursor:pointer; height:30px; margin:0 0 0 8px; color:#000000; font-weight:bold; font-size:20px; width:172px;}

#maske {  position:absolute;  left:0;  top:0;  z-index:9000;  background-color:#000;  display:none;}
#boxese .windowe {  position:absolute;  left:0;  top:0;  width:1300px;  height:150px;  display:none;  z-index:9999;  padding:38px 10px 3px 10px;}
#boxese #submite {  width:318px;  height:117px;  padding:10px;  background-color:#ffffff;}
#boxese #submite a{ text-decoration:none; color:#000000; font-weight:bold; font-size:20px;}
#donee {border:0 none; cursor:pointer; height:30px; margin-left:-17px; margin-top:-29px; width:474px; float:left; }
#closee { border:0 none; cursor:pointer; height:30px; margin:0 0 0 8px; color:#000000; font-weight:bold; font-size:20px; width:172px;}

#maskreq {  position:absolute;  left:0;  top:0;  z-index:9000;  background-color:#000;  display:none;}
#boxesreq .windowreq {  position:absolute;  left:0;  top:0;  width:1300px;  height:150px;  display:none;  z-index:9999;  padding:38px 10px 3px 10px;}
/*#boxesreq #submitreq {  width:789px;  height:640px;  padding:10px;  background-color:#ffffff;}*/
#boxesreq #submitreq {  width:789px;  height:550px;;  padding:10px;  background-color:#ffffff;}
#donereq {border:0 none; cursor:pointer; height:30px; margin-top:-31px; float:right; width:160px; }
#closereq { border:0 none; cursor:pointer; height:30px; margin:0 0 0 8px; color:#000000; font-weight:bold; font-size:20px; width:160px;}

#maskpl { position:absolute;  left:0;  top:0;  z-index:9000;  background-color:#000;  display:none;}
#boxespl .windowpl {  position:absolute;  left:0;  top:0;  width:350px;  height:150px;  display:none;  z-index:9999;  padding:20px;}
#boxespl #submitpl {  width:545px;  height:190px;  padding:10px;  background-color:#ffffff;}
#boxespl #submitpl a{ text-decoration:none; color:#000000; font-weight:bold; font-size:20px;}
#donepl {border:0 none;cursor:pointer;padding:0; color:#000000; font-weight:bold; font-size:20px; margin:0 auto; margin-top:6px;}
#closepl {border:0 none;cursor:pointer;height:30px;margin-left:59px;padding:0;float:left;}

#maskvrecdone {  position:absolute;  left:0;  top:0;  z-index:9000;  background-color:#000;  display:none;}
#boxesvrecdone .windowvrecdone {  position:absolute;  left:0;  top:0;  width:1300px;  height:150px;  display:none;  z-index:9999;  padding:38px 10px 3px 10px;}
#boxesvrecdone #submitvrecdone {  width:318px;  height:117px;  padding:10px;  background-color:#ffffff;}
#boxesvrecdone #submitvrecdone a{ text-decoration:none; color:#000000; font-weight:bold; font-size:20px;}
#donevrecdone {border:0 none; cursor:pointer; height:30px; margin-left:-17px; margin-top:-29px; width:474px; float:left; }

#maskex { position:absolute;  left:0;  top:0;  z-index:9000;  background-color:#000;  display:none;}
#boxesex .windowex {  position:absolute;  left:0;  top:0;  width:350px;  height:150px;  display:none;  z-index:9999;  padding:20px;}
#boxesex #submitex {  width:372px;  height:164px;  padding:10px;  background-color:#ffffff;}
#boxesex #submitex a{ text-decoration:none; color:#000000; font-weight:bold; font-size:20px;}
#doneex {border:0 none;cursor:pointer;padding:0; color:#000000; font-weight:bold; font-size:20px; margin:0 auto; margin-top:6px;}
#closeex {border:0 none;cursor:pointer;height:30px;margin-left:59px;padding:0;float:left;}

#maskinvite { position:absolute;  left:0;  top:0;  z-index:9000;  background-color:#000;  display:none;}
#boxesinvite .windowinvite {  position:absolute;  left:0;  top:0;  width:350px;  height:150px;  display:none;  z-index:9999;  padding:20px;}
#boxesinvite #submitinvite {  width:400px;  height:200px;  padding:10px;  background-color:#ffffff;}
#boxesinvite #submitinvite a{ text-decoration:none; color:#000000; font-weight:bold; font-size:20px;}
#doneinvite {border:0 none;cursor:pointer;padding:0; color:#000000; font-weight:bold; font-size:20px; margin:0 auto; margin-top:6px;}
#closeinvite {border:0 none;cursor:pointer;height:30px;margin-left:59px;padding:0;float:left;}

#maskvm {  position:absolute;  left:0;  top:0;  z-index:9000;  background-color:#000;  display:none;}
#boxesvm .windowvm {  position:absolute;  left:0;  top:0;  width:1300px;  height:150px;  display:none;  z-index:9999;  padding:38px 10px 3px 10px;}
#boxesvm #submitvm {  width:345px;  height:144px;  padding:10px;  background-color:#ffffff;}
#boxesvm #submitvm a{ text-decoration:none; color:#000000; font-weight:bold; font-size:20px;}
#donevm {border:0 none; cursor:pointer; height:30px; margin-left:-17px; margin-top:-29px; width:474px; float:left; }
#closevm {  margin: -2px -4px 7px 158px;;  width:167px;}
</style>
<link href="cam.css" rel="stylesheet" type="text/css" />
<link href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700|Open+Sans+Condensed:700" rel="stylesheet" type="text/css" />
<link rel="stylesheet" media="all" type="text/css" href="<?php echo Juri::base(); ?>components/com_camassistant/skin/css/jquery1.css" />
<script type="text/javascript" src="<?php echo Juri::base(); ?>components/com_camassistant/skin/js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Juri::base(); ?>components/com_camassistant/skin/js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo Juri::base(); ?>components/com_camassistant/skin/js/jquery.elastic.js"></script>

<link rel="stylesheet" media="all" type="text/css" href="<?php echo Juri::base(); ?>components/com_camassistant/skin/css/jquery1.css" />		
<script type="text/javascript" src="<?php echo Juri::base(); ?>components/com_camassistant/skin/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="<?php echo Juri::base(); ?>components/com_camassistant/skin/js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Juri::base(); ?>components/com_camassistant/skin/js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">
H = jQuery.noConflict();
H(document).ready( function(){
H('#searchofrm').submit(function(){
var companyname = H("#companyname").val();
companyname = companyname.trim();
	if(companyname == '' || companyname == 'Enter Company Name'){
	alert("Please enter the company name");
	} else if(companyname.length < 4){
	alert("Please enter at least 4 characters");	
	} else {
	H('#results').addClass('loader');
	H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=checkcompany", {cname: ""+companyname+""}, function(data){
		if(data) {
		 //document.getElementById("preferred-vendorsfirst").style.marginTop="50px";
		H('#results').html(data).slideDown('slow');
		H('#results').removeClass('loader');
		} else {
		H('#results').removeClass('loader');
		}
	});
	}
	return false; 
	});
});

//To add the vendor as preferred vendor
function sendinvitation(){
//H('#companyid'+id).html('Adding...');
var matchesc = [];
var matchesb = [];
var countc = 0 ;
H(".coworkers:checked").each(function() {
    matchesc.push(this.value);
	countc++ ;
});
if(countc == '0'){
alert("Please make a selection to ADD the vendors.");
} else {
	H(".coworkers:checked").each(function() {
			myString = this.value ;
			var myArray = myString.split('-');
			matchesb.push(myArray[1]);
			});
			matchesb = matchesb.join(',') ;
H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=addvendor", {vendorid: ""+matchesb+""}, function(data){
	
	if(data){
	location.reload();
	}
});
}
}

//To add the master myvendors 
function addtomastermyvendors(){
var matchesc = [];
var matchesb = [];
var countc = 0 ;
H(".preferredvendors:checked").each(function() {
    matchesc.push(this.value);
	countc++ ;
});
if(countc == '0'){
alert("Please make a selection to ADD the vendors.");
} else {
	H(".preferredvendors:checked").each(function() {
			matchesb.push(this.value);
		});
			matchesb = matchesb.join(',') ;
			
H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=check_myvendorlist",{vendorid: ""+matchesb+""}, function(data){
		if(data==1)
		geterror_popup();
else {
H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=addtomyvendor", {vendorid: ""+matchesb+""}, function(data){
	if(data){
	window.location ="index.php?option=com_camassistant&controller=vendorscenter&task=mastermyvendors&Itemid=279";
	}
});
}
});
}

}

function geterror_popup(){
        H= jQuery.noConflict();
		var maskHeight = H(document).height();
		var maskWidth = H(window).width();
		H('#maskex').css({'width':maskWidth,'height':maskHeight});
		H('#maskex').fadeIn(100);
		H('#maskex').fadeTo("slow",0.8);
		var winH = H(window).height();
		var winW = H(window).width();
		H("#submitex").css('top',  winH/2-G("#submitex").height()/2);
		H("#submitex").css('left', winW/2-G("#submitex").width()/2);
		H("#submitex").fadeIn(2000);
		H('.windowex #cancelex').click(function (e) {
		e.preventDefault();
		H('#maskex').hide();
		H('.windowex').hide();
		});

	}

//To add the vendor as preferred vendor
function sendpreferredvendor_invitation(){
//H('#companyid'+id).html('Adding...');
var matchesc = [];
var matchesb = [];
var countc = 0 ;
H(".coworkers:checked").each(function() {
    matchesc.push(this.value);
	countc++ ;
});
if(countc == '0'){
alert("Please make a selection to ADD the vendors.");
} else {
	H(".coworkers:checked").each(function() {
			myString = this.value ;
			var myArray = myString.split('-');
			matchesb.push(myArray[1]);
			});
			matchesb = matchesb.join(',') ;
H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=addpreferredvendor", {vendorid: ""+matchesb+""}, function(data){
	if(data){
	location.reload();
	}
});
}
}


function sendinvitationcorporate(){
//H('#companyid'+id).html('Adding...');
var matchesc = [];
var matchesb = [];
var countc = 0 ;
H(".corporates:checked").each(function() {
    matchesc.push(this.value);
	countc++ ;
});
if(countc == '0'){
alert("Please make a selection to ADD the vendors.");
} else {
	H(".corporates:checked").each(function() {
			myString = this.value ;
			var myArray = myString.split('-');
			matchesb.push(myArray[1]);
			});
			matchesb = matchesb.join(',') ;
			
H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=addvendor", {vendorid: ""+matchesb+""}, function(data){
	
	if(data){
	location.reload();
	}
});
}
}

function excludecovendor(){
L = jQuery.noConflict();
var matches = [];
var matchesex = [];
var counte = 0 ;
L(".coworkers:checked").each(function() {
    matches.push(this.value);
	counte++ ;
});
if(counte == '0'){
alert("Please make a selection to EXCLUDE the vendors.");
} else {
		L('body,html').animate({
				scrollTop: 250
				},800);
		var maskHeight = L(document).height();
		var maskWidth = L(window).width();
		L('#maske').css({'width':maskWidth,'height':maskHeight});
		L('#maske').fadeIn(100);
		L('#maske').fadeTo("slow",0.8);
		var winH = L(window).height();
		var winW = L(window).width();
		//L("#submitv").css('top',  '300');
		//L("#submitv").css('left', '582');
		L("#submite").css('top',  winH/2-L("#submite").height()/2);
		L("#submite").css('left', winW/2-L("#submite").width()/2);
				
		L("#submite").fadeIn(2000);
		L('.windowe #donee').click(function (e) {
		e.preventDefault();
		L('#maske').hide();
		L('.windowe').hide();

		L(".coworkers:checked").each(function() {
			myString = this.value ;
			var myArray = myString.split('-');
			matchesex.push(myArray[0]);
			});
			matchesex = matchesex.join(',') ;
			
		H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=excludecovendor", {vendorid: ""+matchesex+""}, function(data){
		if(data==' removed'){
		location.reload();
		} else{
		alert("Not able to exculde the vendor. Please contact support team. ");
		}
	});
	//location.reload();
		});
		L('.windowe #closee').click(function (e) {
		e.preventDefault();
		L('#maske').hide();
		L('.windowe').hide();
		});
 }
}


//To delete vendor from preferred vendors list
function deletevendor(){
L = jQuery.noConflict();
var matches = [];
var matchesa = [];
var countp = 0 ;
L(".preferredvendors:checked").each(function() {
    matches.push(this.value);
	countp++ ;
});
if(countp == '0'){
alert("Please make a selection to REMOVE the vendors.");
} else {
		L(".preferredvendors:checked").each(function() {
				matchesa.push(this.value);
			});
		matchesa = matchesa.join(',') ;
		H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=checkvendorpre", {vendorid: ""+matchesa+""}, function(datares){
		datas = datares.trim();
			if( datas == 'cannot' ) {
				geterrorpopuptodelete();
			} else{
				getnormalpopup(matchesa);
			}
		});
	}
}
//To delete vendor from preferred vendors list
function deletereferredvendors_list(){
L = jQuery.noConflict();
var matches = [];
var matchesa = [];
var countp = 0 ;
L(".preferredvendors:checked").each(function() {
    matches.push(this.value);
	countp++ ;
});
if(countp == '0'){
alert("Please make a selection to REMOVE the vendors.");
} else {
		L(".preferredvendors:checked").each(function() {
				matchesa.push(this.value);
			});
		matchesa = matchesa.join(',') ;
		H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=checkvendorpre", {vendorid: ""+matchesa+""}, function(datares){
		datas = datares.trim();
			if( datas == 'cannot' ) {
				geterrorpopuptodelete();
			} else {
				getnormalpopup_master(matchesa);
			}
		});
	}
}

	function geterrorpopuptodelete(){
		var maskHeight = L(document).height();
		var maskWidth = L(window).width();
		L('#maskpl').css({'width':maskWidth,'height':maskHeight});
		L('#maskpl').fadeIn(100);
		L('#maskpl').fadeTo("slow",0.8);
		var winH = L(window).height();
		var winW = L(window).width();
		L("#submitpl").css('top',  winH/2-L("#submitpl").height()/2);
		L("#submitpl").css('left', winW/2-L("#submitpl").width()/2);
		L("#submitpl").fadeIn(2000);
		L('.windowpl #cancelpl').click(function (e) {
		e.preventDefault();
		L('#maskpl').hide();
		L('.windowpl').hide();
		});
	}
	function getnormalpopup(matchesa){
		var maskHeight = L(document).height();
		var maskWidth = L(window).width();
		L('#maskv').css({'width':maskWidth,'height':maskHeight});
		L('#maskv').fadeIn(100);
		L('#maskv').fadeTo("slow",0.8);
		var winH = L(window).height();
		var winW = L(window).width();
		//L("#submitv").css('top',  '300');
		//L("#submitv").css('left', '582');
		L("#submitv").css('top',  winH/2-L("#submitv").height()/2);
		L("#submitv").css('left', winW/2-L("#submitv").width()/2);
		L("#submitv").fadeIn(2000);
		L('.windowv #donev').click(function (e) {
		e.preventDefault();
		L('#maskv').hide();
		L('.windowv').hide();
		L.post("index2.php?option=com_camassistant&controller=vendorscenter&task=removevendor", {vendorid: ""+matchesa+""}, function(data){
				if(data==1){
				location.reload(); 
				} else {
				alert("Not able to delete the vendor. Please contact support team. ");
				}
			});
		});
		L('.windowv #closev').click(function (e) {
		e.preventDefault();
		L('#maskv').hide();
		L('.windowv').hide();
		});
	}

	function getnormalpopup_master(matchesa){
		var maskHeight = L(document).height();
		var maskWidth = L(window).width();
		L('#maskvm').css({'width':maskWidth,'height':maskHeight});
		L('#maskvm').fadeIn(100);
		L('#maskvm').fadeTo("slow",0.8);
		var winH = L(window).height();
		var winW = L(window).width();
		//L("#submitv").css('top',  '300');
		//L("#submitv").css('left', '582');
		L("#submitvm").css('top',  winH/2-L("#submitv").height()/2);
		L("#submitvm").css('left', winW/2-L("#submitv").width()/2);
		L("#submitvm").fadeIn(2000);
		L('.windowvm .continue_delpre').click(function (e) {
		e.preventDefault();
		L('#maskvm').hide();
		L('.windowvm').hide();
		L.post("index2.php?option=com_camassistant&controller=vendorscenter&task=removevendor_preferredlist", {vendorid: ""+matchesa+""}, function(data){
				if(data){
				location.reload(); 
				} else {
				alert("Not able to delete the vendor. Please contact support team. ");
				}
			});
		});
		L('.windowvm .cancel_delpre').click(function (e) {
		e.preventDefault();
		L('#maskvm').hide();
		L('.windowvm').hide();
		});
	}

function excludevendor(){
L = jQuery.noConflict();
var matches = [];
var matchese = [];
var counte = 0 ;
L(".preferredvendors:checked").each(function() {
    matches.push(this.value);
	counte++ ;
});
if(counte == '0'){
alert("Please make a selection to EXCLUDE the vendors.");
} else {
		L('body,html').animate({
				scrollTop: 250
				},800);
		var maskHeight = L(document).height();
		var maskWidth = L(window).width();
		L('#maske').css({'width':maskWidth,'height':maskHeight});
		L('#maske').fadeIn(100);
		L('#maske').fadeTo("slow",0.8);
		var winH = L(window).height();
		var winW = L(window).width();
		//L("#submitv").css('top',  '300');
		//L("#submitv").css('left', '582');
		L("#submite").css('top',  winH/2-L("#submite").height()/2);
		L("#submite").css('left', winW/2-L("#submite").width()/2);
				
		L("#submite").fadeIn(2000);
		L('.windowe #donee').click(function (e) {
		e.preventDefault();
		L('#maske').hide();
		L('.windowe').hide();
		L(".preferredvendors:checked").each(function() {
			matchese.push(this.value);
			});
			matchese = matchese.join(',') ;
		H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=excludevendor", {vendorid: ""+matchese+""}, function(data){
		if(data==1){
		location.reload();
		} else {
		alert("Not able to exculde the vendor. Please contact support team. ");
		}
	});
	//location.reload();
		});
		L('.windowe #closee').click(function (e) {
		e.preventDefault();
		L('#maske').hide();
		L('.windowe').hide();
		});
 }
}


	function unsubscribevendor(){
		L = jQuery.noConflict();
		L('body,html').animate({
				scrollTop: 250
				},800);
		var maskHeight = L(document).height();
		var maskWidth = L(window).width();
		L('#maskun').css({'width':maskWidth,'height':maskHeight});
		L('#maskun').fadeIn(100);
		L('#maskun').fadeTo("slow",0.8);
		var winH = L(window).height();
		var winW = L(window).width();
		//L("#submitv").css('top',  '300');
		//L("#submitv").css('left', '582');
		L("#submitun").css('top',  winH/2-L("#submitun").height()/2);
		L("#submitun").css('left', winW/2-L("#submitun").width()/2);
				
		L("#submitun").fadeIn(2000);
		L('.windowun #doneun').click(function (e) {
		e.preventDefault();
		L('#maskun').hide();
		L('.windowun').hide();
		});
	}

function county(){
	var state = H("#stateid").val();
	if(state != '0'){
	H('.height_county').show();
	H('#divcounty').show();
	} else {
	H('.height_county').hide();
	H('#divcounty').hide();
	}
	H.post("index2.php?option=com_camassistant&controller=rfp&task=ajaxcounty", {State: ""+state+""}, function(data){
	if(data.length >0) {
	if(data.length == '46'){
	H("#divcounty").css("opacity",'0.5');
	} else {
	H("#divcounty").css("opacity",'');
	}
	H("#divcounty").html(data);
	H("#divcounty").val('<?php echo $_REQUEST['divcounty']; ?>');
	}
	});
}

function precounty(){
	var state = H("#stateid").val();
	H.post("index2.php?option=com_camassistant&controller=rfp&task=ajaxcounty", {State: ""+state+""}, function(data){
	if(data.length >0) {
	if(data.length == '46'){
	H("#divcounty").css("opacity",'0.5');
	} else {
	H("#divcounty").css("opacity",'');
	}
	H("#divcounty").html(data);
	H("#divcounty").val('<?php echo $_REQUEST['divcounty']; ?>');
	}
	});
	document.forms["selectform"].submit();
}

function changecomplince(){
	document.forms["selectform"].submit();
}
function changeverification(){
	document.forms["selectform"].submit();
}
// To send the invitation
function invitevendor(){
	el='<?php  echo Juri::base(); ?>index.php?option=com_camassistant&controller=vendorscenter&task=sendinvitation';
	var options = $merge(options || {}, Json.evaluate("{handler: 'iframe', size: {x: 672, y:600}}"))
	SqueezeBox.fromElement(el,options);
}
//Function to get the values
function specific(val){
	//document.getElementById('managertype').value = val ;
	document.forms["selectform"].submit();
}
//Function to get the industry based records
function specindus(val){
	//document.getElementById('industrytype').value = val ;
	document.forms["selectform"].submit();
}
function speccounty(val){
	document.forms["selectform"].submit();
}
function sendupdateemail(email,companyname,id){
	H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=sendupdateemail&Email="+email+"&cname="+companyname+"&vendorid="+id+"", {Email: ""+email+""}, function(data){
	if(data == 1) {
	alert("Mail sent successfully.");
	} else {
	alert("Please send once again");
	}
	});
}
function getcompstatus(vendorid,status){
	if( status == 'fail' )
		height = '240';
	else
		height = '800';	
	el='<?php  echo Juri::base(); ?>index.php?option=com_camassistant&controller=vendorscenter&task=preferredcompliance&vendorid='+vendorid+'&status='+status+'';
	var options = $merge(options || {}, Json.evaluate("{handler: 'iframe', size: {x: 650, y:"+height+"}}"))
	SqueezeBox.fromElement(el,options);
	}
	function basicrequest(from){
		L = jQuery.noConflict();
		var matches = [];
		var matchesa = [];
		var countp = 0 ;
		var newid = null;
		L(".preferredvendors:checked").each(function() {
			matches.push(this.value);
			countp++ ;
		});
		L(".coworkers:checked").each(function() {
			matches.push(this.value);
			countp++ ;
		});
		L(".corporates:checked").each(function() {
			matches.push(this.value);
			countp++ ;
		});
	if(countp == '0'){
	alert("Please select at least one Vendor to include in this request.");
	} else {
		L(".preferredvendors:checked").each(function() {
		matchesa.push(this.value);		
		});
		L(".coworkers:checked").each(function() {
		myString = this.value ;
		var myArray = myString.split('-');
		matchesa.push(myArray[0]);
		});
		L(".corporates:checked").each(function() {
		myString = this.value ;
		var myArray = myString.split('-');
		matchesa.push(myArray[0]);
		});

	matchesa = matchesa.join(',') ;
	L('#selected_vendors').val(matchesa)  ;		
	
	/*el='<?php  //echo Juri::base(); ?>index2.php?option=com_camassistant&controller=rfp&task=basicrequest';
	var options = $merge(options || {}, Json.evaluate("{handler: 'iframe', size: {x: 672, y:600}}"))
	SqueezeBox.fromElement(el,options);*/
		L = jQuery.noConflict();
		L('body,html').animate({
				scrollTop: 250
				},800);
		var maskHeight = L(document).height();
		var maskWidth = L(window).width();
		L('#maskreq').css({'width':maskWidth,'height':maskHeight});
		L('#maskreq').fadeIn(100);
		L('#maskreq').fadeTo("slow",0.8);
		var winH = L(window).height();
		var winW = L(window).width();
		//L("#submitv").css('top',  '300');
		//L("#submitv").css('left', '582');
		L("#submitreq").css('top',  winH/2-L("#submitreq").height()/2);
		L("#submitreq").css('left', winW/2-L("#submitreq").width()/2);
				
		L("#submitreq").fadeIn(2000);
		L('.windowreq #donereq').click(function (e) {
			//Validation part
			if( L('#property_id').val() == '' || L('#property_id').val() == '0' ){
				alert("Please select a Property from the list.");
				return false;
			}
			else if( L('#projectName').val() == '' ){
				alert("Please enter Reference name.");
				return false;
			}
			else if( L('#proposalDueDate').val() == '' ){
				alert("Please enter Requested Due Date.");
				return false;
			}
			else if( L('#scopeofwork').val() == '' ){
				alert("Please enter Scope of work.");
				return false;
			}
			else{
			//alert("can");
			L(document).ready(function (){
			L("#loading-div-background").show();
			});
			L('#basicrequest').submit();
			}
		e.preventDefault();
		L('#maskreq').hide();
		L('.windowreq').hide();
		});
		L('.windowreq #closereq').click(function (e) {
		e.preventDefault();
		L('#maskreq').hide();
		L('.windowreq').hide();
		});
		
}
L('#property_id').change(function(){
		if( L(this).val() != '0' )
		L( this ).prev().addClass( 'active' );
		else
		L( this ).prev().removeClass( 'active' );
	});
	
L('#projectName').keyup(function(){
		if( L(this).val() == '' )
		L( this ).prev().removeClass( 'active' );
		else
		L( this ).prev().addClass( 'active' );
	});
	L('#proposalDueDate').click(function(){
		L( this ).prev().addClass( 'active' );
	});
	
	L('#scopeofwork').keyup(function(){
		if( L(this).val() == '' )
		L( this ).prev().removeClass( 'active' );
		else
		L( this ).prev().addClass( 'active' );
	});	
}	


function inviteto_basicrequest(type,basics){
	L = jQuery.noConflict();
	var matches = [];
	var matchesa = [];
	var countp = 0 ;
	var newid = null;
		L(".preferredvendors:checked").each(function() {
			matches.push(this.value);
			countp++ ;
		});
		L(".corporates:checked").each(function() {
			matches.push(this.value);
			countp++ ;
		});
		L(".coworkers:checked").each(function() {
			matches.push(this.value);
			countp++ ;
		});
		
	if(basics == 'no')
	var height = '250' ;
	else
	height = '320';
	if(countp == '0'){
		alert("Please select at least one Vendor to invite to an existing Basic Request");
	}
	else{
			L(".preferredvendors:checked").each(function() {
			matchesa.push(this.value);		
			});
			L(".corporates:checked").each(function() {
			myString = this.value ;
			var myArray = myString.split('-');
			matchesa.push(myArray[0]);
			});
			L(".coworkers:checked").each(function() {
			myString = this.value ;
			var myArray = myString.split('-');
			matchesa.push(myArray[0]);
			});
		
		matchesa = matchesa.join(',') ;
		
		el='<?php  echo Juri::base(); ?>index.php?option=com_camassistant&controller=vendorscenter&task=getbasicrequests&vendors='+matchesa;
		var options = $merge(options || {}, Json.evaluate("{handler: 'iframe', size: {x: 650, y:"+height+"}}"))
		SqueezeBox.fromElement(el,options);
	}
}

// Function to recommend vendors to other managers
function vendor_recommend(height){
	L = jQuery.noConflict();
	var matchesr = [];
	var matchesar = [];
	var countpr = 0 ;
	var newidr = null;
		L(".preferredvendors:checked").each(function() {
			matchesr.push(this.value);
			countpr++ ;
		});
		L(".corporates:checked").each(function() {
			matchesr.push(this.value);
			countpr++ ;
		});
		L(".coworkers:checked").each(function() {
			matchesr.push(this.value);
			countpr++ ;
		});
		
	if(countpr == '0'){
		alert("Please select at least one Vendor to recommend.");
	}	
	else{
			L(".preferredvendors:checked").each(function() {
			matchesar.push(this.value);		
			});
			L(".corporates:checked").each(function() {
			myString = this.value ;
			var myArray = myString.split('-');
			matchesar.push(myArray[0]);
			});
			L(".coworkers:checked").each(function() {
			myString = this.value ;
			var myArray = myString.split('-');
			matchesar.push(myArray[0]);
			});
		matchesar = matchesar.join(',') ;
		el='<?php  echo Juri::base(); ?>index.php?option=com_camassistant&controller=vendorscenter&task=getallmanagersrecommend&vendors='+matchesar;
		var options = $merge(options || {}, Json.evaluate("{handler: 'iframe', size: {x: 650, y:"+height+"}}"))
		SqueezeBox.fromElement(el,options);
	}
}

// Function to send the mail to vendors
function vendor_mails(){
	L = jQuery.noConflict();
	var matchesr = [];
	var matchesar = [];
	var countpr = 0 ;
	var newidr = null;
		L(".preferredvendors:checked").each(function() {
			matchesr.push(this.value);
			countpr++ ;
		});
		L(".corporates:checked").each(function() {
			matchesr.push(this.value);
			countpr++ ;
		});
		L(".coworkers:checked").each(function() {
			matchesr.push(this.value);
			countpr++ ;
		});
		
	if(countpr == '0'){
		alert("Please select at least one Vendor to send the mail.");
	}	
	else {
			L(".preferredvendors:checked").each(function() {
			matchesar.push(this.value);		
			});
			L(".corporates:checked").each(function() {
			myString = this.value ;
			var myArray = myString.split('-');
			matchesar.push(myArray[0]);
			});
			L(".coworkers:checked").each(function() {
			myString = this.value ;
			var myArray = myString.split('-');
			matchesar.push(myArray[0]);
			});
		matchesar = matchesar.join(',') ;
		el='<?php  echo Juri::base(); ?>index.php?option=com_camassistant&controller=vendorscenter&task=sendmail_vendors_new&vendors='+matchesar;
		var options = $merge(options || {}, Json.evaluate("{handler: 'iframe', size: {x: 650, y: 480}}"))
		SqueezeBox.fromElement(el,options);
	}
}


function addEventa2(id2)
	{
			L = jQuery.noConflict();
			var arrlicen2=new Array();
			var ni2 = document.getElementById('newdiva2'+id2);
			var numi2 = document.getElementById('theValue');
			var num2 = (document.getElementById("theValue").value -1)+ 2;
			numi2.value = num2;
			var divIdName2 = "newSelector"+num2;
			minheight = L( '.windowreq' ).height() ;
            newitem2='<table><tr><input type="hidden" name="old_docids[]" /><td><span id="delimg'+id2+''+num2+'" style="display:none" title="Remove From RFP"><img src="<?php echo Juri::base(); ?>templates/camassistant_left/images/red.png" alt="delete" style="cursor:pointer;" onclick="javascript:deletelineupload('+id2+''+num2+','+num2+');"/></span></td><td><span id="uploadfile'+id2+''+num2+'" style="float:left;width:auto;padding-right:5px; font-size:14px; color:#8FD800;"></span></td><input type="hidden" value=" " name="linetask_uploads_2'+id2+'[]" id="lineuploads'+id2+''+num2+'"  ></tr></table>';
			var newdiva2 = document.createElement('div');
			newdiva2.setAttribute("id",divIdName2);
			newdiva2.innerHTML = newitem2;
			ni2.appendChild(newdiva2);
			/*nextheight = parseInt(minheight + 20) ;
			L('.windowreq').css('height',nextheight+'px');*/
			linetaskupload(id2+''+num2);
	}
	function linetaskupload(id){
		L = jQuery.noConflict();
		property_id = L('#property_id').val();
		if( L('#property_id').val() == '' || L('#property_id').val() == '0' )
			{
				alert('Please Select the Property.');
			}
		else
			{
				el='<?php  echo Juri::base(); ?>index2.php?option=com_camassistant&controller=rfp&task=upload_select&taskid='+id+'&pid='+property_id+'&mid='+<?php echo $user->id; ?>;
				var options = $merge(options || {}, Json.evaluate("{handler: 'iframe', size: {x: 700, y:330}}"))
				SqueezeBox.fromElement(el,options);
			}
	}
	function deletelineupload(taskid,num){
		var res = confirm("Are you sure you want to remove this file from the RFP?");
			if(res==true){
				window.parent.document.getElementById('lineuploads'+taskid).value ='';
				window.parent.document.getElementById('delimg'+taskid).style.display ='none';
				window.parent.document.getElementById('uploadfile'+taskid).style.display ='none';
				window.parent.document.getElementById('newSelector'+num).style.display ='none';
			}
	}	
	
	H(document).ready( function(){
	H('.rejectrecommendations').click(function(){
	 H("#loading-div-background").show();
		//H(this).addClass('loader');
		var recid = H(this).attr('rel');
				H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=rejectrecs", {Id: ""+recid+""}, function(data){
					location.reload();
				});	
	});
	H('.acceptrecommendations').click(function(){
	 H("#loading-div-background").show();
		//H(this).addClass('loader');
		var totalid = H(this).attr('rel');
		var bothids = totalid.split('-');
		// To check the vendor is already in his list
		H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=checkiniteslist", {Id: ""+bothids[0]+""}, function(data){
			if(data == ' yes'){
				L = jQuery.noConflict();
				L('body,html').animate({
				scrollTop: 250
				},800);
				var maskHeight = L(document).height();
				var maskWidth = L(window).width();
				L('#maskvrec').css({'width':maskWidth,'height':maskHeight});
				L('#maskvrec').fadeIn(100);
				L('#maskvrec').fadeTo("slow",0.8);
				var winH = L(window).height();
				var winW = L(window).width();
				//L("#submitv").css('top',  '300');
				//L("#submitv").css('left', '582');
				L("#submitvrec").css('top',  winH/2-L("#submitvrec").height()/2);
				L("#submitvrec").css('left', winW/2-L("#submitvrec").width()/2);
				
				L("#submitvrec").fadeIn(2000);
				L('.windowvrec #closevrec').click(function (e) {
				H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=rejectrecs&from=accept", {Id: ""+bothids[1]+""}, function(data){
				location.reload();
				});
				e.preventDefault();
				L('#maskvrec').hide();
				L('.windowvrec').hide();
				});
			}
			else{
				H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=addvendor", {vendorid: ""+bothids[0]+""}, function(data){
				if(data){
				H.post("index2.php?option=com_camassistant&controller=vendorscenter&task=rejectrecs&from=accept", {Id: ""+bothids[1]+""}, function(data){
				location.reload();
				});
				
				}
				});
		
			}
		});
				
		
	});
	
	H('#selectall_preferredvendors').click(function(){
		if( H("#selectall_preferredvendors").prop("checked") == true )
		H(".preferredvendors").attr("checked", true);
		else
		H(".preferredvendors").attr("checked", false);
	});
	
	H('#selectall_corporates').click(function(){
		if( H("#selectall_corporates").prop("checked") == true )
		H(".corporates").attr("checked", true);
		else
		H(".corporates").attr("checked", false);
	});
	
	H('#selectall_coworkers').click(function(){
		if( H("#selectall_coworkers").prop("checked") == true )
		H(".coworkers").attr("checked", true);
		else
		H(".coworkers").attr("checked", false);
	});
	
	});

function unverified(vendorid,type){
	if(type == 'unverified')
	var height = '290';
	if(type == 'nonc')
	var height = '245';
	if(type == 'both')
	var height = '350';
	if(type == 'un')
	var height = '224';
	else
	var height = '270';
var el ='index.php?option=com_camassistant&controller=rfpcenter&task=vendortype&vendorid='+vendorid+'&type='+type;
var options = $merge(options || {}, Json.evaluate("{handler: 'iframe', size: {x: 670, y:"+height+"}}"))
SqueezeBox.fromElement(el,options);
if(type == 'un' || type == 'unverified' )
H("#sbox-content").addClass("newunverified_red");
}


function senderrormsg(){
	alert("This Vendor has been Blocked by your Company's Master Account holder");
}
	
function getstandards(vendorid,status){
if(status == '')
	var height = '240';
	else
	var height = '600';
el='<?php  echo Juri::base(); ?>index.php?option=com_camassistant&controller=vendorscenter&task=preferredcompliance&vendorid='+vendorid+'&status='+status+'';
	var options = $merge(options || {}, Json.evaluate("{handler: 'iframe', size: {x: 650, y:"+height+"}}"))
	SqueezeBox.fromElement(el,options);
	if( status == 'Compliant' )
	G("#sbox-window").addClass("newclasssate_green");	
	else
	G("#sbox-window").addClass("newclasssate");
		
}	
L = jQuery.noConflict();
function getpopupbox(){
	L('body,html').animate({
	scrollTop: 250
	},800);
	var maskHeight = L(document).height();
	var maskWidth = L(window).width();
	L('#maskvrecdone').css({'width':maskWidth,'height':maskHeight});
	L('#maskvrecdone').fadeIn(100);
	L('#maskvrecdone').fadeTo("slow",0.8);
	var winH = L(window).height();
	var winW = L(window).width();
	L("#submitvrecdone").css('top',  winH/2-L("#submitvrecdone").height()/2);
	L("#submitvrecdone").css('left', winW/2-L("#submitvrecdone").width()/2);
	
	L("#submitvrecdone").fadeIn(2000);
	L('.windowvrecdone #closevrecdone').click(function (e) {
	
		e.preventDefault();
		L('#maskvrecdone').hide();
		L('.windowvrecdone').hide();
	});
}

function notificationfornoncom(count){
	if(count>0){
		L("#loading-div-background").show();
		//window.location="index2.php?option=com_camassistant&controller=vendorscenter&task=sendnotificationto_noncompilance";
		L.post("index2.php?option=com_camassistant&controller=vendorscenter&task=sendnotificationto_noncompilance", function(data){
		if(trim(data)=='success'){
			location.reload();
			}
		});
	} else {
		geterror_popup_unverified();
	}
}

function geterror_popup_unverified(){
	var maskHeight = L(document).height();
	var maskWidth = L(window).width();
	L('#maskinvite').css({'width':maskWidth,'height':maskHeight});
	L('#maskinvite').fadeIn(100);
	L('#maskinvite').fadeTo("slow",0.8);
	var winH = L(window).height();
	var winW = L(window).width();
	L("#submitinvite").css('top',  winH/2-L("#submitinvite").height()/2);
	L("#submitinvite").css('left', winW/2-L("#submitinvite").width()/2);
	L("#submitinvite").fadeIn(2000);
	L('.windowinvite #cancelinvite').click(function (e) {
		e.preventDefault();
		L('#maskinvite').hide();
		L('.windowinvite').hide();
	});
}
	
L(document).ready( function(){
	L( "#filters" ).click(function() {
	  L( ".optional_filters" ).toggle( "slow" );
	  L(this).toggleClass('active');
	});
});
</script>
</head>
<body>
<br />
<div id="add-vendor">
<div id="results" class="companies">
</div>

<div class="clr"></div>

<div id="loading-div-background">
  <div id="loading-div" class="ui-corner-all">
    <img style="height:32px;width:32px;margin:30px;" src="templates/camassistant_left/images/loading_icon.gif" alt="Loading.."/><br>Scolding your Non-Compliant Vendors.
  </div>
</div>


<div id="recommendations">
<?php 
if($recommends){ for( $r=0; $r<count($recommends); $r++ ){?>
<div class="managerrecs">
	<div class="acceptrecs"><a href="javascript:void(0);" rel="<?php echo $recommends[$r]->vendorid.'-'.$recommends[$r]->id; ?>" class="acceptrecommendations" title="Add to your 'My Vendors' list"></a></div>
	<div class="recsname">
	<span><?php echo $recommends[$r]->sendername; ?></span> has recommended this Vendor to you:
	<h2> - <a class="profilerecs" href="index.php?option=com_camassistant&controller=vendors&task=vendordetailslayout&id=<?php echo $recommends[$r]->vendorid; ?>" target="_blank"><?php echo $recommends[$r]->vendorname; ?></a> - </h2>
	</div>
	<div class="rejectrecs"><a href="javascript:void(0);" rel="<?php echo $recommends[$r]->id; ?>" class="rejectrecommendations" title="Remove vendor recommendation"></a></div>
</div>
<?php } 
echo "<br /><br />"; 
}
?>
</div>

<div id="preferred"><?php if($this->permision!='yes'){ ?>
<div class="notify_noncompilant">
<?php $nonco=0; if($this->items_non){ for( $rn=0; $rn<count($this->items_non); $rn++ ){
			if( ( $this->items_non[$rn]->per_vendor == 'show' || $this->items_non[$rn]->star_vendor == 'show' ) && $this->items_non[$rn]->my_vendor == 'hide' )
				$display_pre_1 = 'none';
			else
				$display_pre_1 = '';
			
			if($this->items_non[$rn]->final_status=='fail' && $display_pre_1!='none' && $this->items_non[$rn]->vendor_documents != 'pending'){
				$nonco++;
			}
		} } //echo $nonco; ?>
	<!--<p><a href="javascript:notificationfornoncom(<?php echo $nonco; ?>);">click here for notifications</a></p>-->
<div class="banner_grp">
<div class="banner_new_left">
<div class="content_bannr_left">
<h3>You have <span><?php echo $nonco; ?></span> Non-Compliant Vendors.</h3>
<p>Notify them so they can fix it:</p>
<a href="javascript:notificationfornoncom(<?php echo $nonco; ?>);"><button><img src="templates/camassistant_left/images/messege_new.png" width="17" height="10" />&nbsp; NOTIFY MY NON-COMPLIANT VENDORS</button></a>
</div>
</div>
<div class="banner_new_right"><img src="templates/camassistant_left/images/ban_new_right.png" width="271" height="181" /></div>
</div>
</div><?php } ?>
	<div class="clr"></div>
  <div id="preferred-vendorsfirst" class="breakclass" style="">
  <?php /*?><div class="preferredvendors-head">
      <h5 style="float:left; background-image:none;">MY PREFERRED VENDORS</h5>
	  <a href="index2.php?option=com_content&amp;view=article&amp;id=250&amp;Itemid=113" rel="{handler: 'iframe', size: {x: 680, y: 530}}" class="modal" title="Click here" mce_style="text-decoration: none;" style="text-decoration: none;"><img src="templates/camassistant_left/images/preferred-arrow.jpg" style="float:right;"> </a>
	<div class="clr"></div>  
      </div><?php */?>
	  
	  
<?php if($user->user_type == '13' && $user->accounttype == 'master'){ ?>
 <div id="i_bar_yellow" style=" background: #e6b613; box-shadow: 1px 2px 1px #808080; height: 34px; margin-bottom: 10px; text-align: center;">
 <?php } else { ?>
 <div id="i_bar" style="background: #77b800 none repeat scroll 0 0;">
 <?php } ?>
<!--<div id="i_icon">
<a href="index2.php?option=com_content&amp;view=article&amp;id=250&amp;Itemid=113" rel="{handler: 'iframe', size: {x: 680, y: 530}}" class="modal" title="Click here" mce_style="text-decoration: none;" style="text-decoration: none;"><img src="templates/camassistant_left/images/info_icon2.png" style="float:right;"> </a>
</div>-->
    <div id="i_bar_txt" style="text-align:center; padding:8px 0 0 0px; width:675px;">
<span>
<?php 
$user =& JFactory::getUser();
if($user->user_type == '13' && $user->accounttype == 'master') 
echo "<font style='font-weight:bold; color:#fff;'>CORPORATE PREFERRED VENDORS</font>";
else
echo "<font style='font-weight:bold;'>MY VENDORS</font>";
?>
</span><div id="filters">&nbsp;&nbsp;&nbsp;&nbsp;FILTERS<div class="arrow-right"></div></div><div class="optional_filters optional_filters_mar"><p style="height:5px;"></p><form name="select_form" id="selectform" method="post" action="">
<div align="center">
<span style="color:#7AB800; font-weight:bold; text-align:center">OPTIONAL FILTERS</span>
<table cellspacing="0" cellpadding="0">
<tbody>
<tr><td>    
	  <select name="state" style="width:400px; margin-left:0px;" id="stateid" onchange="javascript:precounty();" >
			 <option value="0">All States</option>
			<?php
			for ($i=0; $i<count($statelist); $i++){
			$states = $statelist[$i];   ?>
			<option  value="<?php echo $states->state_id; ?>" <?php if($states->state_id==$_REQUEST['state']){ ?> selected="selected" <?php } ?> ><?php echo $states->state_name; ?> </option>
			<?php }  ?>
	  </select>
	  </td></tr>
	
	  <tr><td>
	  <select style="width: 400px; margin-left:0px;margin-right:5px; opacity:0.5; display:none;" name="divcounty" id="divcounty" onchange="javascript:speccounty()" >
<option value="">Select County</option>
</select>
 <script type="text/javascript">
county();
</script>
</td></tr>
<tr><td>
    <select style="margin-left:0px; width:400px;margin-right:0px; word-wrap:normal;" name="industrytype" onchange="javascript:specindus('')">
      <option value="">All Industries</option>
	  <?php
	  for($i=0; $i<count($this->industries); $i++){
	  ?> 
<option <?php if($industrytype == $this->industries[$i]->id){ echo "selected"; } ?> value="<?php echo $this->industries[$i]->id; ?>"> <?php echo $this->industries[$i]->industry_name; ?>  </option>
	  <?php }
	  ?>
      </select>
	  </td></tr>
<tr><td>    
	  <select name="compliance" style="width:400px; margin-left:0px;" id="compliance" onchange="javascript:changecomplince();" >
			 <option value="0" <?php if($_REQUEST['compliance'] == '0'){ ?> selected="selected" <?php } ?>>All Compliance Statuses</option>
			 <option value="comp" <?php if($_REQUEST['compliance'] == 'comp'){ ?> selected="selected" <?php } ?>>Compliant</option>
			 <option value="noncomp" <?php if($_REQUEST['compliance'] == 'noncomp'){ ?> selected="selected" <?php } ?>>Non-Compliant</option>
	  </select>
	  </td></tr>
<tr><td>    
	  <select name="verification" style="width:400px; margin-left:0px;" id="compliance" onchange="javascript:changeverification();" >
			 <option value="0" <?php if($_REQUEST['verification'] == '0'){ ?> selected="selected" <?php } ?>>All Account Types</option>
			 <option value="ver" <?php if($_REQUEST['verification'] == 'ver'){ ?> selected="selected" <?php } ?>>Verified</option>
			 <option value="unver" <?php if($_REQUEST['verification'] == 'unver'){ ?> selected="selected" <?php } ?>>Unverified</option>
	  </select>
	  </td></tr>
	<input type="hidden" name="option" value="com_camassistant" />
	<input type="hidden" name="controller" value="vendorscenter" />
	<input type="hidden" name="view" value="vendorscenter" />
	<input type="hidden" name="task" value="vendorscenter" />
	<!--<input type="hidden" name="managertype" id="managertype" value="" />
	<input type="hidden" name="industrytype" id="industrytype" value="" />	-->
	</tbody></table>
	</div>
	</form>
	</div></div>
</div>

<?php 
	$sort = JRequest::getVar('sort','');
	$type = JRequest::getVar('type','');
	
	if( $sort == 'asc' && $type == 'preferred' ){
	$id = 'compliant_desc' ;
	$sort = 'desc';
	}
	else if( $sort == 'desc' && $type == 'preferred' ){
	$id = 'compliant_asc' ;
	$sort = 'asc';
	}
	else{
	$sort = 'asc';
	$id = 'compliant_nosort' ;
	}
	?>
<div id="heading_vendors" style="background:#ececec;">
<div class="checkbox_vendor"><input type="checkbox" value="" name="selectall" id="selectall_preferredvendors" />SELECT</div>
<div class="company_vendor"><a id="<?php echo $id; ?>" href="index.php?option=com_camassistant&controller=vendorscenter&task=vendorscenter&view=vendorscenter&Itemid=242&type=preferred&sort=<?php echo $sort ; ?>">COMPANY</a></div>
<div class="apple_vendor">APPLE RATING</div>
<div class="compliant_vendor" style="padding-left:3px;">COMPLIANCE STATUS</div>
</div>
<?php


$star_vendors = $this->corporatevendors_star ;

if($star_vendors){
	foreach($star_vendors as $star){
		$stars[] = $star->v_id;
	}
}

?>
 <p style="height:3px;"></p>
   <div class="clr"></div>
  </div>
  <div class="totalvendorspre_preferred">
<?php  //echo '<pre>'; print_r($this->items);
$items = $this->items;
$firmids = $this->firmids ;
//echo "<pre>"; print_r($items ); echo "</pre>";exit;
$count_corporate = 0;
if($items) {
foreach($items as $am ) {  

		if(($user->user_type == '13' && $user->accounttype == 'master') || $user->user_type == '16') {
			if( ( $am->subscribe_type == 'free' || $am->subscribe_type == '' ) && $am->unverified == 'hide' )
				$display_block = 'none';
			else
				$display_block = '';
				
			if( ($am->final_status == 'fail' || $am->final_status == 'medium') && $am->block_nonc == 'hide' )
				$display_nonc = 'none';
			else
				$display_nonc = '';
				
			if( $am->per_vendor == 'hide' && $am->star_vendor == 'hide' )
				$display_pre = 'none';
			else
				$display_pre = '';
			
				
		}
		else{
			$display_nonc = '';
			$display_block = '';
			$display_pre ='';
				
		}

	if( $compliance_filter == 'comp' )
		{
			if( $am->final_status != 'success' )
			$display_comp = 'none';
			else
			$display_comp = '';
		}
	else if( $compliance_filter == 'noncomp' )
		{
			if( $am->final_status == 'fail' || $am->final_status == 'medium'  || !$am->final_status)
			$display_noncomp = '';
			else
			$display_noncomp = 'none';
		}
			
if( $display_block == 'none' || $display_comp == 'none' ||  $display_noncomp == 'none' || $display_nonc == 'none' || $display_pre == 'none' ){		
	$final_display = 'none';
	
	}
else{
	$final_display = '';	
	$count_corporate ++ ;
	}
?> 

<?php
	$checkbox = '';
	if( $am->unverified == 'hide' && $am->block_nonc == 'hide' )
		{
			if( ( $am->subscribe_type == 'free' || $am->subscribe_type == '' ) && ( $am->final_status == 'fail' || $am->final_status == 'medium') ){
			$args = 'both';
			}
			else if( ( $am->subscribe_type == 'free' || $am->subscribe_type == '' ) && $am->final_status == 'success' ){
			$args = 'un';
			}
			else if( ( $am->subscribe_type != 'free' || $am->subscribe_type != '' )&& ( $am->final_status == 'fail' || $am->final_status == 'medium') ){
			$args = 'nonc';
			}
			else{
			$checkbox = 'show';
			}
			
		}
	else if( $am->unverified == 'hide' )
		{
			if( $am->subscribe_type == 'free' || $am->subscribe_type == '' ){
			$args = 'un';
			}
			else{
			$checkbox = 'show';
			}
		}
	else if( $am->block_nonc == 'hide' )
		{
			if( $am->final_status == 'fail' || $am->final_status == 'medium' ){
			$args = 'nonc';
			}	
			else {
			$checkbox = 'show';	
			}
		}	
	else {
		$args = '';
		$checkbox = 'show';	
	}	

?>		 
	  
	  
  <div id="preferredvendors<?php echo  $am->vid; ?>" style="display:<?php echo $display; ?><?php echo $final_display; ?>">
  <div id="preferredvendorsinvitations">
   <div class="search-panel-middlepre checkbox_vendor">
      <?php 
	  if( ( $am->subscribe_type == 'free' || $am->subscribe_type == '' ) && $am->unverified == 'hide' && $user->accounttype == 'master'){ ?>
	  <a href="javascript:senderrormsg();"><img src="templates/camassistant_left/images/Block2.png" /></a>
	  <?php }
	  else if( $checkbox != 'show' ) {  ?>
	  <a href="javascript:unverified(<?php echo $am->v_id; ?>,'<?php echo $args; ?>');" style="margin-left:-17px;"><img src="templates/camassistant_left/images/Block2.png" /></a>
	  <?php  } 
      else if($managertype != '2'){ ?>
	  <input type="checkbox" value="<?php echo  $am->vid; ?>" name="preferred" class="preferredvendors<?php echo $final_display; ?>" style="margin-left:-15px;" />
	   <?php } 
	  else { ?>
	  <a title="Add to My Vendors" href="javascript:sendinvitation(<?php echo  $am->v_id; ?>,'<?php echo $am->inhousevendors; ?>');" class="pre-red"><strong><img src="templates/camassistant_left/images/addicon.png" /></strong></a>
	  <?php } ?>
	  <br />
	<span id="removing<?php echo  $am->vid; ?>" style="color:#6DAA00; font-weight:bold;"></span>
      </div>
    <div class="search-panel-left_rfp company_vendor">

	   <ul>
        <li>
		
		<?php 
		$user =& JFactory::getUser();
		if (in_array($am->id, $stars)){ ?>
		<img src="templates/camassistant_left/images/star-icon.png"  title="Corporate Preferred Vendor" />
		<?php }
		else{
		}
		?><strong><a href="index.php?option=com_camassistant&controller=vendors&task=vendordetailslayout&id=<?php echo $am->id; ?>" target="_blank"><?php echo $am->company_name; ?></a></strong></li>
        <li><?php echo $am->name . ' ' .$am->lastname; ?> <?php echo $am->company_phone; ?>	<?php if($am->phone_ext){ echo "&nbsp;Ext.&nbsp;".$am->phone_ext; } else { echo ""; } ?></li>
		<?php
		$db = & JFactory::getDBO();
	$statecode  = "SELECT code from #__cam_vendor_states where id=".$am->state." " ;  
	$db->setQuery($statecode);
	$statea = $db->loadResult(); 
	?>
        <li><?php echo $am->city; ?>,&nbsp;<?php echo strtoupper($statea); ?></li>
        <li><a style="font-weight:normal; color:gray;" class="miniemails" href="mailto:<?php echo $am->inhousevendors.  '?cc=' .$am->ccemail; ?>">Email</a></li>
        </ul>
	  
	  
		
      </div>
	 
    <div class="search-panel-right_rfp apple_vendor">
	<?php
	$db = & JFactory::getDBO();
	$ratecount = "SELECT V.apple FROM `#__cam_vendor_proposals` as U, `#__cam_rfpinfo` as V where U.proposedvendorid=".$am->id." and V.apple!=0 and V.apple_publish=0 and U.proposaltype='Awarded' and U.rfpno = V.id ";
	$db->setQuery($ratecount);
	$count_vs=$db->loadObjectList();
	//To get the CAMA rAting
		$camratingf = "SELECT camrating FROM `#__users` where id=".$am->id."  ";
		$db->setQuery($camratingf);
		$cam_ratingf = $db->loadResult();
		
	if($count_vs){
		for($c=0; $c<count($count_vs); $c++){
		$total = $total + $count_vs[$c]->apple ;
		}
		$camrating = "SELECT camrating FROM `#__users` where id=".$am->id."  ";
		$db->setQuery($camrating);
		$cam_rating = $db->loadResult();
		
		if($cam_rating) {
		$total = $total + $cam_rating ;
		$count = count($count_vs) + 1;
		$avgrating = $total  / $count;	
		$rating =  round($avgrating, 1); 
		}
		else {
		$avgrating = $total  / count($count_vs);	
		$rating =  round($avgrating, 1); 
		}
	}
	else if($cam_ratingf){
	$rating = round($cam_ratingf, 1); 
	}
	else{
	$rating = 4 ;
	}
	
	if ($rating > 0 && $rating <= 0.50)
			{ $rate_image = $rateimage.'5.png';  $rating='0.5'; }
			elseif ($rating > 0.50 && $rating <= 1.00)
			{ $rate_image = $rateimage.'10.png'; $rating='1'; }
			elseif ($rating > 1.00 && $rating <= 1.50)
			{ $rate_image = $rateimage.'15.png'; $rating='1.5';}
			elseif ($rating > 1.50 && $rating <= 2.00)
			{ $rate_image = $rateimage.'20.png'; $rating='2';}
			elseif ($rating > 2.00 && $rating <= 2.50)
			{ $rate_image = $rateimage.'25.png'; $rating='2.5';}
			elseif ($rating > 2.50 && $rating <= 3.00)
			{ $rate_image = $rateimage.'30.png'; $rating='3';}
			elseif ($rating > 3.00 && $rating <= 3.50)
			{ $rate_image = $rateimage.'35.png'; $rating='3.5';}
			elseif ($rating > 3.50 && $rating <= 4.00)
			{ $rate_image = $rateimage.'40.png'; $rating='4';}
			elseif ($rating > 4.00 && $rating <= 4.50)
			{ $rate_image = $rateimage.'45.png'; $rating='4.5';}
			elseif ($rating > 4.50 && $rating <= 5.00)
			{ $rate_image = $rateimage.'50.png'; $rating='5';}
			else
			{ $rate_image = $rateimage.'40.png'; $rating='4';}
			$total = 0;

	?>
			<img width="130" src="components/com_camassistant/assets/images/rating/vendorrating/<?php echo $rate_image; ?>" />
			
	</div>
	
				<?php
				if($permission == 'yes'){
					$text = "N/A";
					$id = 'nostandards';
				}
				else{
				   
				   if( $am->vendor_documents == 'pending' ){
					$id = 'pendingicon';
					$title = 'Verification Pending';
					$text_status = 'Pending';
					}
					else if($am->final_status == 'fail' || $am->termsandc == 'fail' || $am->acount_type == 'show' ) {
					//$text = "NON-COMPLIANT";
					$id = 'noncompliant';
					$title = 'Non-Compliant';
					$text_status = 'Non-Compliant';
					}
					else if($am->final_status == 'success'){
					//$text = "COMPLIANT";
					$id = 'compliant';
					$title = 'Compliant';
					$text_status = 'Compliant';
					}
					else if($am->final_status == 'medium'){
					//$text = "COMPLIANT & NON-COMPLIANT";
					$id = 'mediumcompliant';
					$title = 'Compliant & Non-Compliant';
					$text_status = 'Compliant and Non-Compliant';
					}
					 else{
						$text = "N/A";
						$id = 'nostandards';
					}
				}
				?>

	<div class="search-panel-image_rfp compliant_vendor" style="padding-left:16px;">
	  	  <p align="center" style="color:; display:block; margin-bottom:7px; font-weight:bold; padding-right:0px;">
		 <?php  if($globe != 'fail'){ ?>
			<a href="javascript:void(0);" onclick="getstandards('<?php echo $am->id; ?>','<?php echo $text_status; ?>');" id="<?php echo $id; ?>" title="<?php echo $title; ?>"><?php echo $text; ?></a>
			<?php } else { ?>
			<a id="nostandards" href="javascript:void(0);" onclick="javascript:getcompstatus(<?php echo $am->id; ?>,'<?php echo $globe; ?>');" title="No Standards">N/A</a>
			<?php 
			}
			 $id = '';
			$title = '';
			$text_status = '';
			$text = '';

			?>		

<?php    
if( $am->subscribe_type == 'free'  || $am->subscribe_type=='' ) { ?>
<div class="unverifiedvendor"><a href="javascript:void(0);" onclick="unverified(<?php echo $am->id; ?>,'unverified');" title="Click for more info">UNVERIFIED</a></div>
<?php } else {  ?>
<div class="verifiedvendor"><a href="javascript:void(0);" onclick="unverified(<?php echo $am->id; ?>,'verified');" title="Click for more info">VERIFIED</a></div>
<?php } ?>
				
			 </p>
	  </div>
	  
	  
    <div class="clr"></div>
  </div>
  </div>
    <?php } 
	} 
	if( $count_corporate == 0 ) {  ?>
	<p align="center" style="margin-top:20px; font-weight:bold;">There are no vendors available on this list with this sorting.</p>
	<?php }
	?> 
	</div>
	
	<?php 
	if($user->user_type == '13' && $user->accounttype == 'master') {
	$textshows =  "Remove from Corporate Preferred Vendors";
	$addmyvendors ='';
	}
	else{
	$textshows =  "Remove from My Vendors ";
	$addmyvendors ='none';
    }
	
	if($count_corporate >0) { ?>
	<div align="center" style="margin-top:17px;">
	<?php if($user->user_type!= '16')
	{?>
	<?php if($user->user_type == '13' && $user->accounttype == 'master') { ?>
	<a title="<?php echo $textshows; ?>" class="delete_list" href="javascript:deletereferredvendors_list();"></a>
	<?php } else {?>
	<a title="<?php echo $textshows; ?>" class="delete" href="javascript:deletevendor();"></a>
	<?php }?>
	<a title="Add to My Vendors"  style="display:<?php echo $addmyvendors;?>" class="addicon" href="javascript:addtomastermyvendors();"></a>
	<a title="Email Vendor(s)" class="vendor_mails" href="javascript:vendor_mails();"></a>
	<a title="Create a new Basic Request" class="basicrequest" href="javascript:basicrequest('pre');"></a>
	<a title="Invite Vendor(s) to existing Request" class="basicrequest_invite" href="javascript:inviteto_basicrequest('pre','<?php echo $basics; ?>');"></a>
	<a title="Recommend to Co-Workers" class="vendor_recommend" href="javascript:vendor_recommend(<?php echo $height; ?>);"></a>
	<?php }
	
	$user =& JFactory::getUser();
	if($user->accounttype  == 'master'){ ?>
	<a title="Block this Vendor from participating in your Managers' projects" class="exclude" href="javascript:excludevendor();"></a>
	<?php } ?>
	</div>
	<?php } ?>
	
	<?php 
	$user =& JFactory::getUser();
	if($user->accounttype != 'master'){
	 ?>
	<p style="height:50px;"></p>
	<div style="" class="breakclass" id="preferred-vendorsfirst">
  <?php /*?><div class="preferredvendors-head">
      <h5 style="float:left; background-image:none;">CORPORATE PREFERRED VENDORS</h5>
	  <a style="text-decoration: none;" mce_style="text-decoration: none;" title="Click here" class="modal" rel="{handler: 'iframe', size: {x: 680, y: 530}}" href="index2.php?option=com_content&amp;view=article&amp;id=250&amp;Itemid=113"><img style="float:right;" src="/dev/templates/camassistant_left/images/preferred-arrow.jpg"> </a>
	<div class="clr"></div>  
      </div><?php */?>
	  
	 <div id="i_bar_yellow" style=" background: #e6b613; box-shadow: 1px 2px 1px #808080; height: 34px; margin-bottom: 10px; text-align: center;">
<div id="i_icon">
<a style="text-decoration: none;" mce_style="text-decoration: none;" title="Click here" class="modal" rel="{handler: 'iframe', size: {x: 680, y: 530}}" href="index2.php?option=com_content&amp;view=article&amp;id=250&amp;Itemid=113"><img src="templates/camassistant_left/images/info_icon2.png" style="float:right;"> </a>
</div>
    <div id="i_bar_txt" style="text-align:center;  padding:8px 0 0 35px;">
<span><font style="font-weight:bold; color:#fff;">CORPORATE PREFERRED VENDORS</font></span></div>
</div>
	

	<?php 
	$sort = JRequest::getVar('sort','');
	$type = JRequest::getVar('type','');
	
	if( $sort == 'asc' && $type == 'corporate' ){
	$id = 'compliant_desc' ;
	$sort = 'desc';
	}
	else if( $sort == 'desc' && $type == 'corporate' ){
	$id = 'compliant_asc' ;
	$sort = 'asc';
	}
	else{
	$sort = 'asc';
	$id = 'compliant_nosort' ;
	}
	?>
		  
	 <div id="heading_vendors">
<div class="checkbox_vendor"><input type="checkbox" value="" name="selectall" id="selectall_corporates" />SELECT</div> 
<div class="company_vendor">
<a id="<?php echo $id; ?>" href="index.php?option=com_camassistant&controller=vendorscenter&task=vendorscenter&view=vendorscenter&Itemid=242&type=corporate&sort=<?php echo $sort ; ?>">COMPANY</a></div>
<div class="apple_vendor">APPLE RATING</div>
<div class="compliant_vendor" style="padding-left:3px;">COMPLIANCE STATUS</div>
</div> 
	  
<div class="clr"></div>
</div>
<div class="totalvendorspre_preferred">
<?php 
$vendor_first = $this->items ;
if($vendor_first){
	foreach($vendor_first as $vvv){
		$first_vendors[] = $vvv->id;
	}
}

//echo "<pre>"; print_r($first_vendors); echo "<pre>";

$corporate = $this->corporate ;
//echo "<pre>"; print_r($corporate); echo "<pre>";
$count_c_mgr = 0;
 if($corporate) {
foreach($corporate as $am ) {  
	if($ownids){
				if ( in_array($am->v_id, $ownids) )
				  {
				  $display = 'none' ;
				  }
				else
				  {
				  $display = '' ;
				  }
			}
	if($first_vendors){
				if ( in_array($am->v_id, $first_vendors) )
				  {
				  $display = 'none' ;
				  }
				else
				  {
				  $display = '' ;
				  }
			}	
	if( ( $am->subscribe_type == 'free' || $am->subscribe_type == '' ) && $am->unverified == 'hide' )
		$display_block1 = 'none';
	else
		$display_block1 = '';	

	if( ( $am->final_status == 'fail' || $am->final_status == 'medium') && $am->block_nonc == 'hide' )
		$display_nonc = 'none';
	else
		$display_nonc = '';

     if( $am->per_vendor == 'hide' && $am->star_vendor == 'hide' )
				$display_pre = 'none';
			else
				$display_pre = '';

	if( $compliance_filter == 'comp' )
		{
			if( $am->final_status != 'success' )
			$display_comp = 'none';
			else
			$display_comp = '';
		}
	else if( $compliance_filter == 'noncomp' )
		{
			if( $am->final_status == 'fail' || $am->final_status == 'medium' || !$am->final_status )
			$display_noncomp = '';
			else
			$display_noncomp = 'none';
		}
		
				
		if( $display == 'none' || $display_block1 == 'none' || $display_comp == 'none' || $display_noncomp == 'none' || $display_nonc =='none' || $display_pre =='none' ){
			$final_disp = 'none';
			
		}
		else{
			$final_disp = '';
			$count_c_mgr ++;
		}
?> 
<div id="preferredvendors<?php echo  $am->vid; ?>" style="display:<?php echo $final_disp; ?>">
   <div id="preferredvendorsinvitations">
   <div class="search-panel-middlepre checkbox_vendor">
     
	  <?php 
	  if( ( $am->subscribe_type == 'free' || $am->subscribe_type == '' ) && $am->unverified == 'hide' ){ ?>
	  <a href="javascript:senderrormsg();"><img src="templates/camassistant_left/images/Block2.png" /></a>
	  <?php }
	  else{ ?>
	  <input type="checkbox" value="<?php echo  $am->vid; ?>-<?php echo  $am->v_id; ?>" name="corporates" class="corporates<?php echo $final_disp; ?>" />
	  <br />
	  <?php } ?>
	  
	<span id="removing<?php echo  $am->vid; ?>" style="color:#6DAA00; font-weight:bold;"></span>
      </div>
     <div class="search-panel-left_rfp company_vendor">
      <?php //if($am->subscribe == 'yes'){ ?>
	  <ul>
        <li><strong>
		<img src="templates/camassistant_left/images/star-icon.png" title="Corporate Preferred Vendor" /><a style="margin-left:2px;" href="index.php?option=com_camassistant&controller=vendors&task=vendordetailslayout&id=<?php echo $am->id; ?>" target="_blank"><?php echo $am->company_name; ?></a></strong></li>
        <li><?php echo $am->name . ' ' .$am->lastname; ?> <?php echo $am->company_phone; ?>	<?php if($am->phone_ext){ echo "&nbsp;Ext.&nbsp;".$am->phone_ext; } else { echo ""; } ?></li>
		<?php
		$db = & JFactory::getDBO();
	$statecode  = "SELECT code from #__cam_vendor_states where id=".$am->state." " ; 
	$db->setQuery($statecode);
	$statea = $db->loadResult(); 
	?>
        <li><?php echo $am->city; ?>,&nbsp;<?php echo strtoupper($statea); ?></li>
        <li><a style="font-weight:normal; color:gray;" class="miniemails" href="mailto:<?php echo $am->inhousevendors.  '?cc=' .$am->ccemail; ?>">mail</a></li>
        </ul>
		<?php //} ?>
      </div>
	 
    <div class="search-panel-right_rfp apple_vendor">
	<?php
	$db = & JFactory::getDBO();
	$ratecount = "SELECT V.apple FROM `#__cam_vendor_proposals` as U, `#__cam_rfpinfo` as V where U.proposedvendorid=".$am->id." and V.apple!=0 and V.apple_publish=0 and U.proposaltype='Awarded' and U.rfpno = V.id ";
	$db->setQuery($ratecount);
	$count_vs=$db->loadObjectList();
	//To get the CAMA rAting
		$camratingf = "SELECT camrating FROM `#__users` where id=".$am->id."  ";
		$db->setQuery($camratingf);
		$cam_ratingf = $db->loadResult();
		
	if($count_vs){
		for($c=0; $c<count($count_vs); $c++){
		$total = $total + $count_vs[$c]->apple ;
		}
		$camrating = "SELECT camrating FROM `#__users` where id=".$am->id."  ";
		$db->setQuery($camrating);
		$cam_rating = $db->loadResult();
		
		if($cam_rating) {
		$total = $total + $cam_rating ;
		$count = count($count_vs) + 1;
		$avgrating = $total  / $count;	
		$rating =  round($avgrating, 1); 
		}
		else {
		$avgrating = $total  / count($count_vs);	
		$rating =  round($avgrating, 1); 
		}
	}
	else if($cam_ratingf){
	$rating = round($cam_ratingf, 1); 
	}
	else{
	$rating = 4 ;
	}
	
	if ($rating > 0 && $rating <= 0.50)
			{ $rate_image = $rateimage.'5.png';  $rating='0.5'; }
			elseif ($rating > 0.50 && $rating <= 1.00)
			{ $rate_image = $rateimage.'10.png'; $rating='1'; }
			elseif ($rating > 1.00 && $rating <= 1.50)
			{ $rate_image = $rateimage.'15.png'; $rating='1.5';}
			elseif ($rating > 1.50 && $rating <= 2.00)
			{ $rate_image = $rateimage.'20.png'; $rating='2';}
			elseif ($rating > 2.00 && $rating <= 2.50)
			{ $rate_image = $rateimage.'25.png'; $rating='2.5';}
			elseif ($rating > 2.50 && $rating <= 3.00)
			{ $rate_image = $rateimage.'30.png'; $rating='3';}
			elseif ($rating > 3.00 && $rating <= 3.50)
			{ $rate_image = $rateimage.'35.png'; $rating='3.5';}
			elseif ($rating > 3.50 && $rating <= 4.00)
			{ $rate_image = $rateimage.'40.png'; $rating='4';}
			elseif ($rating > 4.00 && $rating <= 4.50)
			{ $rate_image = $rateimage.'45.png'; $rating='4.5';}
			elseif ($rating > 4.50 && $rating <= 5.00)
			{ $rate_image = $rateimage.'50.png'; $rating='5';}
			else
			{ $rate_image = $rateimage.'40.png'; $rating='4';}
			$total = 0;

	?>
			<img width="130" src="components/com_camassistant/assets/images/rating/vendorrating/<?php echo $rate_image; ?>" />
			
	</div>
	
	
		<?php
				if($permission == 'yes'){
					$text = "N/A";
					$id = 'nostandards';
				}
				else{
				
				    if( $am->vendor_documents == 'pending' ){
						$id = 'pendingicon';
						$title = 'Verification Pending';
						$text_status = 'Pending';
					}
					else if($am->final_status == 'fail' || $am->termsandc == 'fail' || $am->acount_type == 'show' ) {
					//$text = "NON-COMPLIANT";
					$id = 'noncompliant';
					$title = 'Non-Compliant';
					}
					else if($am->final_status == 'success'){
					//$text = "COMPLIANT";
					$id = 'compliant';
					$title = 'Compliant';
					}
					else if($am->final_status == 'medium'){
					//$text = "COMPLIANT & NON-COMPLIANT";
					$id = 'mediumcompliant';
					$title = 'Compliant & Non-Compliant';
					}
					 else{
						$text = "N/A";
						$id = 'nostandards';
					}
				}			
			?>
				
		<div class="search-panel-image_rfp compliant_vendor" style="padding-left:16px;">
	  	  <p align="center" style="color:; display:block; margin-bottom:7px; font-weight:bold; padding-right:0px;">
		  <?php  if($globe != 'fail'){ ?>
			<a href="javascript:void(0);" onclick="getstandards('<?php echo $am->v_id; ?>','<?php echo $title; ?>');" id="<?php echo $id; ?>" title="<?php echo $title; ?>"><?php echo $text; ?></a>
			<?php } else { ?>
			<a id="nostandards" href="javascript:void(0);" onclick="javascript:getcompstatus(<?php echo $am->v_id; ?>,'<?php echo $globe; ?>');" title="No Standards">N/A</a>
			<?php }
			 $id = '';
			$title = '';
			$text_status = '';
			$text = '';

			?>

<?php
if( $am->subscribe_type == 'free' || $am->subscribe_type == '' ) { ?>
<div class="unverifiedvendor"><a href="javascript:void(0);" onclick="unverified(<?php echo $am->v_id; ?>,'unverified');" title="Click for more info">UNVERIFIED</a></div>
<?php } else {  ?>
<div class="verifiedvendor"><a href="javascript:void(0);" onclick="unverified(<?php echo $am->v_id; ?>,'verified');" title="Click for more info">VERIFIED</a></div>
<?php } ?>
			
			 </p>
	  </div>
	  	
    <div class="clr"></div>
  </div>
  </div>
   <?php } 
	}   
	if( $count_c_mgr == 0 ){ ?>
	<p align="center" style="margin-top:20px; font-weight:bold;">There are no vendors available on this list with this sorting.</p>
	<?php }
	?>
	</div>
	<?php if($count_c_mgr >0 ) { ?>
	<div align="center" style="margin-top:17px;">
	<a title="Add to My Vendors" class="addicon" href="javascript:sendinvitationcorporate();"></a>
	<a title="Email Vendor(s)" class="vendor_mails" href="javascript:vendor_mails();"></a>
	<a title="Create a new Basic Request" class="basicrequest" href="javascript:basicrequest('cor');"></a>
	<a title="Invite Vendor(s) to existing Request" class="basicrequest_invite" href="javascript:inviteto_basicrequest('cor','<?php echo $basics; ?>');"></a>
	<a title="Recommend to Co-Workers" class="vendor_recommend" href="javascript:vendor_recommend(<?php echo $height; ?>);"></a>
	
	</div>
	<?php } ?>
	
	<?php 
	}
?>

  <p style="height:50px;"></p>

	
	<?php /*?><div class="preferredvendors-head">
      <h5 style="float:left; background-image:none;">CO-WORKER PREFERRED VENDORS</h5>
	  <a href="index2.php?option=com_content&amp;view=article&amp;id=250&amp;Itemid=113" rel="{handler: 'iframe', size: {x: 680, y: 530}}" class="modal" title="Click here" mce_style="text-decoration: none;" style="text-decoration: none;"><img src="templates/camassistant_left/images/preferred-arrow.jpg" style="float:right;"> </a>
	<div class="clr"></div>  
      </div><?php */?>
	<?php 
	$sort = JRequest::getVar('sort','');
	$type = JRequest::getVar('type','');
	
	if( $sort == 'asc' && $type == 'coworker' ){
	$id = 'compliant_desc' ;
	$sort = 'desc';
	}
	else if( $sort == 'desc' && $type == 'coworker' ){
	$id = 'compliant_asc' ;
	$sort = 'asc';
	}
	else{
	$sort = 'asc';
	$id = 'compliant_nosort' ;
	}
	?>

<?php
if($user->user_type == '13' && $user->accounttype == 'master') 
	$dis_cowrker =  'none';
	else
	$dis_cowrker =  '';
	
?>	  
<div style="display:<?php echo $dis_cowrker;?>">	  
	  <div id="i_bar">
<div id="i_icon">
<a href="index2.php?option=com_content&amp;view=article&amp;id=250&amp;Itemid=113" rel="{handler: 'iframe', size: {x: 680, y: 530}}" class="modal" title="Click here" mce_style="text-decoration: none;" style="text-decoration: none;"><img src="templates/camassistant_left/images/info_icon2.png" style="float:right;"> </a>
</div>
    <div id="i_bar_txt" style="text-align:center;  padding:8px 0 0 35px;">
<span><font style="font-weight:bold;">CO-WORKER VENDORS</font></span></div>
</div>

	  
	  <div id="heading_vendors">
<div class="checkbox_vendor"><input type="checkbox" value="" name="selectall" id="selectall_coworkers" />SELECT</div>
<div class="company_vendor"><a id="<?php echo $id; ?>" href="index.php?option=com_camassistant&controller=vendorscenter&task=vendorscenter&view=vendorscenter&Itemid=242&type=coworker&sort=<?php echo $sort ; ?>">COMPANY</a></div>
<div class="apple_vendor">APPLE RATING</div>
<div class="compliant_vendor" style="padding-left:3px;">COMPLIANCE STATUS</div>
</div>
</div>
<div class="totalvendorspre_preferred" style="display:<?php echo $dis_cowrker;?>"> 

  <?php //echo "<pre>"; print_r($firmids); exit; ?>
	  <?php
	  if(!$corporate)
	  $corporate = '';
	  else
	  $corporate = $corporate;
	  
if($corporate){
	foreach($corporate as $cor){
		$corporates[] = $cor->v_id;
	}
}
	  if(!$corporates)
	  $corporates[] = '';
	  else
	  $corporates[] = $corporates;
	 // echo '<pre>';print_r($corporate);exit;
$count_firmids = 0;
	  if($firmids) {
foreach($firmids as $am ) {  


	if($ownids || $corporates){
			if($user->user_type == 13 || ($user->user_type == '12')){
				if( ( $am->per_vendor == 'show' || $am->star_vendor == 'show' ) && $am->my_vendor == 'hide' )
				  {
				  $display = 'none' ;
				  }
				else
				  {
				  $display = '' ;
				  }
			} else 
			if( $user->accounttype != 'master'  ){
				if ( in_array($am->v_id, $ownids) || in_array($am->id, $corporates) )
				  {
				  $display = 'none' ;
				  }
				else
				  {
				  $display = '' ;
				  }
			}
			else{
				if ( in_array($am->v_id, $ownids) )
				  {
				  $display = 'none' ;
				  }
				else
				  {
				  $display = '' ;
				  }
			}	  
					}


	if( $compliance_filter == 'comp' )
		{
			if( $am->final_status != 'success' )
			$display_comp = 'none';
			else
			$display_comp = '';
		}
	else if( $compliance_filter == 'noncomp' )
		{
			if( $am->final_status == 'fail' || $am->final_status == 'medium'  || !$am->final_status )
			$display_noncomp = '';
			else
			$display_noncomp = 'none';
		}

		if( $display == 'none' || $display_comp == 'none' || $display_noncomp == 'none' ){
			$final_disp = 'none';
			
		}
		else{
			$final_disp = '';
			$count_firmids ++ ;
		}	
					
?> 
  <div id="preferredvendors<?php echo  $am->vid; ?>" style="display:<?php echo $final_disp; ?>">
  <div id="preferredvendorsinvitations">
   <div class="search-panel-middlepre checkbox_vendor">
     <?php
	 if($am->v_id)
	 $v_id = $am->v_id ;
	 else
	 $v_id = $am->id ;
		 ?>
		 
<?php
	$checkbox = '';
	$args = '';
	if( $am->unverified == 'hide' && $am->block_nonc == 'hide' )
		{
			if( ( $am->subscribe_type == 'free' || $am->subscribe_type == '' ) && ( $am->final_status == 'fail' || $am->final_status == 'medium') ){
			//$popupfunction = 'nonverified_nonc';
			$args = 'both';
			}
			else if( ( $am->subscribe_type == 'free' || $am->subscribe_type == '' ) && $am->final_status == 'success' ){
			//$popupfunction = 'unverified';
			$args = 'un';
			}
			else if( ( $am->subscribe_type != 'free' || $am->subscribe_type != '' ) && ( $am->final_status == 'fail' || $am->final_status == 'medium') ){
			//$popupfunction = 'verified_nonc';
			$args = 'nonc';
			}
			else{
			$checkbox = 'show';
			}
			
		}
	else if( $am->unverified == 'hide' )
		{
			if( $am->subscribe_type == 'free' || $am->subscribe_type == '' ){
			//$popupfunction = 'unverified';
			$args = 'un';
			}
			else{
			$checkbox = 'show';
			}
		}
	else if( $am->block_nonc == 'hide' )
		{
			if( $am->final_status == 'fail' || $am->final_status == 'medium' ){
			//$popupfunction = 'verified_nonc';
			$args = 'nonc';
			}	
			else {
			$checkbox = 'show';	
			}
		}	
	else {
		$args = '';
		$checkbox = 'show';	
	}	
?>		 
	  <?php if( $checkbox != 'show' )	{ ?>
	  <a href="javascript:unverified(<?php echo $am->id; ?>,'<?php echo $args; ?>');" style="margin-left:-17px;"><img src="templates/camassistant_left/images/Block2.png" /></a>
	 <?php } else { ?>
	  <input type="checkbox" value="<?php echo  $am->vid; ?>-<?php echo  $v_id; ?>" name="coworkers" class="coworkers<?php echo $final_disp; ?>" />
	  <br />
	  <?php } 
	  $checkbox = '';
	  ?>
	<span id="removing<?php echo  $am->vid; ?>" style="color:#6DAA00; font-weight:bold;"></span>
      </div>
     <div class="search-panel-left_rfp company_vendor">
     
	  <ul>
        <li><strong><a href="index.php?option=com_camassistant&controller=vendors&task=vendordetailslayout&id=<?php echo $am->id; ?>" target="_blank"><?php echo $am->company_name; ?></a></strong></li>
        <li><?php echo $am->name . ' ' .$am->lastname; ?> <?php echo $am->company_phone; ?>	<?php if($am->phone_ext){ echo "&nbsp;Ext.&nbsp;".$am->phone_ext; } else { echo ""; } ?></li>
		<?php
		$db = & JFactory::getDBO();
	$statecode  = "SELECT code from #__cam_vendor_states where id=".$am->state." " ; 
	$db->setQuery($statecode);
	$statea = $db->loadResult(); 
	?>
        <li><?php echo $am->city; ?>,&nbsp;<?php echo strtoupper($statea); ?></li>
        <li><a style="font-weight:normal; color:gray;" class="miniemails" href="mailto:<?php echo $am->inhousevendors. '?cc=' .$am->ccemail; ?>">Email</a></li>
        </ul>
		
      </div>
	 
    <div class="search-panel-right_rfp apple_vendor">
	<?php
	$db = & JFactory::getDBO();
	$ratecount = "SELECT V.apple FROM `#__cam_vendor_proposals` as U, `#__cam_rfpinfo` as V where U.proposedvendorid=".$am->v_id." and V.apple!=0 and V.apple_publish=0 and U.proposaltype='Awarded' and U.rfpno = V.id ";
	$db->setQuery($ratecount);
	$count_vs=$db->loadObjectList();
	//To get the CAMA rAting
		$camratingf = "SELECT camrating FROM `#__users` where id=".$am->v_id."  ";
		$db->setQuery($camratingf);
		$cam_ratingf = $db->loadResult();
		
	if($count_vs){
		for($c=0; $c<count($count_vs); $c++){
		$total = $total + $count_vs[$c]->apple ;
		}
		$camrating = "SELECT camrating FROM `#__users` where id=".$am->v_id."  ";
		$db->setQuery($camrating);
		$cam_rating = $db->loadResult();
		
		if($cam_rating) {
		$total = $total + $cam_rating ;
		$count = count($count_vs) + 1;
		$avgrating = $total  / $count;	
		$rating =  round($avgrating, 1); 
		}
		else {
		$avgrating = $total  / count($count_vs);	
		$rating =  round($avgrating, 1); 
		}
	}
	else if($cam_ratingf){
	$rating = round($cam_ratingf, 1); 
	}
	else{
	$rating = 4 ;
	}
	
	if ($rating > 0 && $rating <= 0.50)
			{ $rate_image = $rateimage.'5.png';  $rating='0.5'; }
			elseif ($rating > 0.50 && $rating <= 1.00)
			{ $rate_image = $rateimage.'10.png'; $rating='1'; }
			elseif ($rating > 1.00 && $rating <= 1.50)
			{ $rate_image = $rateimage.'15.png'; $rating='1.5';}
			elseif ($rating > 1.50 && $rating <= 2.00)
			{ $rate_image = $rateimage.'20.png'; $rating='2';}
			elseif ($rating > 2.00 && $rating <= 2.50)
			{ $rate_image = $rateimage.'25.png'; $rating='2.5';}
			elseif ($rating > 2.50 && $rating <= 3.00)
			{ $rate_image = $rateimage.'30.png'; $rating='3';}
			elseif ($rating > 3.00 && $rating <= 3.50)
			{ $rate_image = $rateimage.'35.png'; $rating='3.5';}
			elseif ($rating > 3.50 && $rating <= 4.00)
			{ $rate_image = $rateimage.'40.png'; $rating='4';}
			elseif ($rating > 4.00 && $rating <= 4.50)
			{ $rate_image = $rateimage.'45.png'; $rating='4.5';}
			elseif ($rating > 4.50 && $rating <= 5.00)
			{ $rate_image = $rateimage.'50.png'; $rating='5';}
			else
			{ $rate_image = $rateimage.'40.png'; $rating='4';}
			$total = 0;

	?>
			<img width="130" src="components/com_camassistant/assets/images/rating/vendorrating/<?php echo $rate_image; ?>" />
			
	</div>
	<?php
				if($permission == 'yes'){
					$text = "N/A";
					$id = 'nostandards';
				}
				else{
				    if( $am->vendor_documents == 'pending' ){
						$id = 'pendingicon';
						$title = 'Verification Pending';
						$text_status = 'Pending';
					}
					else if($am->final_status == 'fail' || $am->termsandc == 'fail' || $am->acount_type == 'show' ) {
					//$text = "NON-COMPLIANT";
					$id = 'noncompliant';
					$title = 'Non-Compliant';
					}
					else if($am->final_status == 'success'){
					//$text = "COMPLIANT";
					$id = 'compliant';
					$title = 'Compliant';
					}
					else if($am->final_status == 'medium'){
					//$text = "COMPLIANT & NON-COMPLIANT";
					$id = 'mediumcompliant';
					$title = 'Compliant & Non-Compliant';
					}
					 else{
						$text = "N/A";
						$id = 'nostandards';
					}
				}
			?>
		<div class="search-panel-image_rfp compliant_vendor" style="padding-left:16px">
	  	  <p align="center" style="color:; display:block; margin-bottom:7px; font-weight:bold; padding-right:0px;">
		  <?php 
		  if($am->v_id)
		  $vidc = $am->v_id;
		  else
		  $vidc = $am->id;
			?>
		  <?php  if($globe != 'fail'){ ?>
			<a href="javascript:void(0);" onclick="getstandards('<?php echo $vidc; ?>','<?php echo $title; ?>');" id="<?php echo $id; ?>" title="<?php echo $title; ?>"><?php echo $text; ?></a>
			<?php } else { ?>
			<a id="nostandards" href="javascript:void(0);" onclick="javascript:getcompstatus(<?php echo $vidc; ?>,'<?php echo $globe; ?>');" title="No Standards">N/A</a>
			<?php }
			 $id = '';
			$title = '';
			$text_status = '';
			$text = '';

			?>
<?php
if( $am->subscribe_type == 'free' || $am->subscribe_type == '' ) { ?>
<div class="unverifiedvendor"><a href="javascript:void(0);" onclick="unverified(<?php echo $vidc; ?>,'unverified');" title="">UNVERIFIED</a></div>
<?php } else {  ?>
<div class="verifiedvendor"><a href="javascript:void(0);" onclick="unverified(<?php echo $vidc; ?>,'verified');" title="Click for more info">VERIFIED</a></div>
<?php } ?>			
			 </p>
	  </div>
	  
    <div class="clr"></div>
  </div>
  </div>
    <?php } 
	} 
	if($count_firmids == 0){ ?>
	<p align="center" style="margin-top:20px; font-weight:bold;">There are no vendors available on this list with this sorting.</p>
	<?php }
	?> 
	</div>
	<?php
	if($user->user_type == '13' && $user->accounttype == 'master') {
	$dis_pre =  '';
	}
	else{
	$dis_pre =  'none';
	}
	 if($count_firmids >0 ) { ?>
	<div align="center" style="margin-top:17px; display:<?php echo $dis_cowrker;?>">
	<?php if($user->user_type != '16'){ ?>
	<a title="Add to Corporate Preferred Vendors" class="addpreferredvendoricon" style= "display:<?php echo $dis_pre;?>" href="javascript:sendpreferredvendor_invitation();"></a>
	<a title="Add to My Vendors" class="addicon" href="javascript:sendinvitation();"></a>
	<a title="Email Vendor(s)" class="vendor_mails" href="javascript:vendor_mails();"></a>
	<a title="Create a new Basic Request" class="basicrequest" href="javascript:basicrequest('cow');"></a>
	<a title="Invite Vendor(s) to existing Request" class="basicrequest_invite" href="javascript:inviteto_basicrequest('cow','<?php echo $basics; ?>');"></a>
	<a title="Recommend to Co-Workers" class="vendor_recommend" href="javascript:vendor_recommend(<?php echo $height; ?>);"></a>
	<?php }
	$user =& JFactory::getUser();
	if($user->accounttype  == 'master'){ ?>
	<a title="Block this Vendor from participating in your Managers' projects" class="exclude" href="javascript:excludecovendor();"></a>
	<?php } ?>
	</div>
	<?php } ?>
	
</div>
</div>
</body>
</html>

<div id="boxesv" class="boxesv">
<div id="submitv" class="windowv" style="top:300px; left:582px; border:4px solid #8FD800; position:fixed;">
<br/>
<p align="center" style="color:gray;">This Vendor will be REMOVED from your Vendor List</p>
<div style="padding-top:20px; text-align:center;">
<form name="edit" id="edit" method="post">

<div id="closev" name="closep" value="Cancel"><img src="templates/camassistant_left/images/cancel.gif" /></div>
<div id="donev"  name="donev" value="Ok"><img src="templates/camassistant_left/images/ok.gif" /></div>
</div>
</form>

</div>
  <div id="maskvm"></div>
</div>

<div id="boxesvm" class="boxesvm">
<div id="submitvm" class="windowvm" style="top:300px; left:582px; border:4px solid #8FD800; position:fixed;">
<br/>
<p align="center" style="color:gray;">Are you sure you want to REMOVE this Vendor from your Corporate Preferred Vendor list?</p>
<div class="mainformbutton">
<form name="edit" id="edit" method="post">
<a class="cancel_delpre" href="javascript:void(0);"></a>
<a class="continue_delpre" href="javascript:void(0);"></a>
</div>
</form>

</div>
  <div id="maskv"></div>
</div>

<div id="boxesun" class="boxesun">
<div id="submitun" class="windowun" style="top:300px; left:582px; border:4px solid #8FD800; position:fixed;">
<br/>
<p align="center" style="color:gray;">The Profile Page for this Vendor is not available due to an expired account.</p>
<div style="padding-top:20px; text-align:center;">
<form name="edit" id="edit" method="post">
<div id="doneun"  name="doneun" value="Ok"><img src="templates/camassistant_left/images/OK.gif" /></div>
</div>
</form>

</div>
  <div id="maskun"></div>
</div>


<div id="boxese" class="boxese">
<div id="submite" class="windowe" style="top:300px; left:582px; border:4px solid #8FD800; position:fixed;">
<br/>
<p align="center" style="color:gray;">This Vendor will be BLOCKED from participating in any of your Managers' projects</p>
<div style="padding-top:20px; text-align:center;">
<form name="edit" id="edit" method="post">
<div id="closee" name="closee" value="Cancel"><img src="templates/camassistant_left/images/cancel.gif" /></div>
<div id="donee"  name="donee" value="Ok"><img src="templates/camassistant_left/images/ok.gif" /></div>
</div>
</form>

</div>
  <div id="maske"></div>
</div>

<div id="boxesreq" class="boxesreq">
<div id="submitreq" class="windowreq" style="top:300px; left:582px; border:4px solid #8FD800; position:fixed; overflow-y:scroll;">
<div style="padding:10px 10px 12px; text-align:center;">
<form name="basicrequest" id="basicrequest" method="post">
<div class="light_box">
<div id="i_bar_terms">
<div id="i_bar_txt_terms">
<span> <font style="font-weight:bold; color:#FFF; font-size:14px;">BASIC REQUEST</font></span>
</div></div>


<div class="list_box">
<ul>
<li>
<label class="selecte_property" >Select Property</label>
<?php 
$properties = $this->properties ;
 ?>
	<select id="property_id" name="property_id" style="width:101%; height:32px; padding:5px;">
	<option value="0">Please select property</option>
	<?php
		for( $p=0; $p<count($properties); $p++ ){ ?>
		<option value="<?php echo $properties[$p]->id; ?>"><?php echo str_replace('_',' ',$properties[$p]->property_name); ?></option>
		<?php }
	?>
	</select>
</li>
<li>
<label class="selecte_property" >Reference Name for this Request</label>
<input type="text" name="projectName" id="projectName"  />
</li>
<li>
<label class="selecte_property" >Requested Due Date</label>
<input type="text" name="proposalDueDate" readonly="readonly" id="proposalDueDate" />
</li>
<script type="text/javascript">
H = jQuery.noConflict();
H('#proposalDueDate').datetimepicker({
			dateFormat: 'mm-dd-yy',
			//minDate: '10D',
			minDate: '0D',
			//minDate: 'new',
			 timeFormat: 'hh:00',
			 hour: 12,
			 minute: 00,
			changeYear: true,changeMonth:true,
});

H("#proposalDueDate").click(function () {
			 var someDate = new Date();
			var numberOfDaysToAdd = 7;
			someDate.setDate(someDate.getDate() + numberOfDaysToAdd); 
			var dd = someDate.getDate();
			var mm = someDate.getMonth() + 1;
			var y = someDate.getFullYear();
			var newdate = mm + '-'+ dd + '-'+ y + '12:00';
			 H('#proposalDueDate').datetimepicker('setDate', newdate);
                  });
				  
</script>
<li class="text_areabox">
<label class="selecte_property" >Scope of Work (SOW)</label>
<textarea name="jobnotes" id="scopeofwork"></textarea>
<span id='upload_file10' style="float:left;width:auto;padding-right:5px; margin-top:5px; padding-left:2px;"><a class="upload_new_files_rfp" href="javascript:addEventa2('10');">
<p style="height:10px;"></p></a></span>
<span id="delimg10" style="display:none" title="Remove From RFP"><img src="templates/camassistant_left/images/red.png" alt="delete" style="cursor:pointer; margin-top:13px;" onclick="javascript:deletelineupload_line(10);"  /></span>
<div class="clear"></div>
<div id="newdiva210" style="margin-top:10px;"></div>
<input name="hidden" type="hidden" id="theValue" value="0">
<input name="hidden" type="hidden" id="idval" value="0">

</li>
<div id="topborder_row"></div>
<li class="buttons_basic"> 
<div id="closereq" name="closereq" value="Cancel"> <a class="cancel_basci_submit" href="javascript:void(0);"></a></div>
<div id="donereq"  name="donereq" value="Ok"><a class="submit_basci_submit" href="javascript:void(0);"></a></div>
</li>
</ul>
</div>
</div>
</div>
<input type="hidden" name="option" value="com_camassistant" />
<input type="hidden" name="controller" value="rfp" />
<input type="hidden" name="task" value="submit_rfp" />
<input type="hidden" name="rfp_type" value="rfp" />
<input type="hidden" name="basicrequest" value="basicrequest" />
<input type="hidden" name="selected_vendors" id="selected_vendors" value="" />
</form>

</div>
  <div id="maskreq"></div>
</div>


<div id="boxesvrec" class="boxesvrec">
<div id="submitvrec" class="windowvrec" style="top:300px; left:582px; border:4px solid #8FD800; position:fixed;">
<br/>
<p align="center" style="color:gray;">This Vendor already exists in your "My Vendors" list"</p>
<div style="padding-top:20px; text-align:center;">
<form name="edit" id="edit" method="post">
<div id="closevrec" name="closeprec" value="Cancel"><img src="templates/camassistant_left/images/OK.gif" /></div>
</div>
</form>
</div>
<div id="maskvrec"></div>
</div>

<div id="loading-div-background">
  <div id="loading-div" class="ui-corner-all">
    <img style="height:32px;width:32px;margin:30px;" src="templates/camassistant_left/images/loading_icon.gif" alt="Loading.."/><br>Please wait while your request is being submitted.
  </div>
</div>


<div id="boxespl" style="top:576px; left:582px;">
<div id="submitpl" class="windowpl" style="top:300px; left:582px; border:6px solid red; position:fixed">
<div id="i_bar_terms" style="background:none repeat scroll 0 0 red;">
<div id="i_bar_txt_terms" style="padding-top:8px; font-size:14px;">
<span style="font-size:14px;"> <font style="font-weight:bold; color:#FFF;">ERROR</font></span>
</div></div>
<div style="text-align:justify"><p class="wrongrequestmsg_remove">This Vendor has purchased an active Preferred Vendor Code.  As a result, you cannot remove them from the Corporate Preferred Vendor list. Once you cancel a code, you may then manually remove any Vendors who purchased that code from this list.</p>
</div>
<div style="padding-top:20px;" align="center">
<div id="cancelpl" name="donepl" value="Ok" class="existing_code_preferred"></div>
</div>
</div>
  <div id="maskpl"></div>
</div>


<div id="boxesvrecdone" class="boxesvrecdone">
<div id="submitvrecdone" class="windowvrecdone" style="top:300px; left:582px; border:6px solid #8FD800; position:fixed;">
<br/>
<p align="center" style="color:gray; font-size:13px;">Vendor(s) recommended successfully</p>
<div class="recoommend_alert">
<div id="closevrecdone" name="closeprecdone" value="Cancel" class="ok_newone_recom"></div>
</div>
</div>
<div id="maskvrecdone"></div>
</div>

<div id="boxesex" style="top:576px; left:582px;">
<div id="submitex" class="windowex" style="top:300px; left:582px; border:6px solid red; position:fixed">
<div id="i_bar_terms" style="background:none repeat scroll 0 0 red; margin-top: 7px;">
<div id="i_bar_txt_terms" style="padding-top:8px; font-size:14px;">
<span style="font-size:14px;"> <font style="font-weight:bold; color:#FFF;">ERROR</font></span>
</div></div>
<div style="text-align:justify"><p class="Corporate_status">This Vendor is already added as a My Vendor</p>
</div>
<div style="padding-top:22px;" align="center">
<div id="cancelex" name="doneex" value="Ok" class="Corporate_statusbutton"></div>
</div>
</div>
  <div id="maskex"></div>
</div>

<div id="boxesinvite" style="top:576px; left:582px;">
<div id="submitinvite" class="windowinvite" style="top:300px; left:582px; border:6px solid red; position:fixed">
<div id="i_bar_terms" style="background:none repeat scroll 0 0 red; margin-top: 7px;">
<div id="i_bar_txt_terms" style="padding-top:8px; font-size:14px;">
<span style="font-size:14px;"> <font style="font-weight:bold; color:#FFF;">ERROR</font></span>
</div></div>
<div style="text-align:justify"><p class="Corporate_status" style="margin:20px auto 0;">There aren’t any Non-Compliant Vendors to send this notification to.  Please add more Vendors to your MY VENDORS list or invite more Vendors to register by clicking “<strong>INVITE A VENDOR</strong>”</p>
</div>
<div style="padding-top:22px;" align="center">
<div id="cancelinvite" name="doneinvite" value="Ok" class="Corporate_statusbutton"></div>
</div>
</div>
  <div id="maskinvite"></div>
</div>
