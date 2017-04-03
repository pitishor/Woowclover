var jq = jQuery;
var id, woowprice;
var resp;

// flexible format currency function. 

 function formatCurrency(input, n, x, s, c) {
   var num = parseFloat(input);

   var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = num.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};



//waiting on change event. onchange event only fires after onblur event and we want to give feedback to the user before the end of editing of the field. 

jQuery(document).on('input', '.WOOWInputPrice', function(event){
  var lblName = "lblStatus" + event.target.name;
  var lblStatus = document.getElementById(lblName);
  lblStatus.innerHTML = "Tab or click anywhere else to save."
  lblStatus.style.color = "blue";
  lblStatus.style.visibility = 'visible';

});

// we make the donation button in PHP but we can't select the value of the item so we set it in javascript. 
jQuery(document).on('input', '.donationAmtSelect', function(event){
  document.getElementById(event.target.name).value = event.target.value;
});

//RT media's upload of .. media behaves like this hiding and showing their buttons on focus and blur events. 
jQuery(document).on('focus', '#whats-new-textarea', function(event){
  document.getElementsByClassName('bpfb_actions_container')[0].style.display = "initial";
  document.getElementById('bpfb_addPhotos').style.display = "none";
});

////RT media's upload of .. media behaves like this hiding and showing their buttons on focus and blur events. 
//jQuery(document).on('blur', '#whats-new-textarea', function(event){
//  document.getElementsByClassName('bpfb_actions_container')[0].style.display = "none";
//  document.getElementById('bpfb_addPhotos').style.display = "none";
//});




//This function marks an item that was purchased / donation made to list it in the gallery. 
jQuery(document).on('click', '.BuyWoowPaypalButton , .WOOWBuyButton', function(event){
   	resp= jq.post(ajaxurl, {
          action: 'record_woow_plusone_sale',
          'metaname': 'woowplusone',
          'actvity_id': event.target.name
	
      });
});


jQuery(document).on('mouseup', '.button.acomment-reply.bp-primary-action', function(event){
  event.target.parentNode.parentNode.nextElementSibling.nextElementSibling.style.display  = 'block';
  console.log(event.target.parentNode.parentNode.nextElementSibling.nextElementSibling);
  
});



//This function posts back to the server asyncrhonioudly a price update. 
//This function gets binded to the onblur event from the actual button which sends the Activity ID from html produced in WP since the activity ID is present there as a variable. 
function AsyncWOOWPriceUpdate( btn, AID ){
  var lblStatus = document.getElementById("lblStatus"+AID);

  btn.value = formatCurrency (btn.value , 2, 3, ',', '.');

  var regex  = /(?=.)^\$?(([1-9][0-9]{0,2}(,[0-9]{3})*)|0)?(\.[0-9]{1,2})?$/;

  if (regex.test(btn.value) || btn.value==''){

	resp= jq.post(ajaxurl, {
          action: 'woow_update_price',
          'metaname': 'woowprice',
          'metavalue': btn.value,
          'actvity_id': AID
	
      });
  
var w = window.innerWidth
|| document.documentElement.clientWidth
|| document.body.clientWidth;

var h = window.innerHeight
|| document.documentElement.clientHeight
|| document.body.clientHeight; 
   
	lblStatus.innerHTML = "Price Saved";
  lblStatus.style.color = "red";

  setTimeout(clearLblStatus, 5000, lblStatus);

  var i = 0;
  	  for (i = 0; i < 12; i++) { 
		  setTimeout(blink , i * 300,lblStatus, i);
		  
	  }
  }else{

      lblStatus.innerHTML = "Invalid U.S. Dollar Amount";
      lblStatus.style.color = "red";

  }	


};


//clean up 
function clearLblStatus(lblStatus){
    lblStatus.innerHTML =""
};

//visual efects
  function blink(lblStatus , i) {
      lblStatus.style.color  = (i%2 == 0) ? 'green' : 'red';
	
  };

function browserInf(){
  
var w = window.innerWidth
|| document.documentElement.clientWidth
|| document.body.clientWidth;

var h = window.innerHeight
|| document.documentElement.clientHeight
|| document.body.clientHeight; 

document.getElementById("test").innerHTML = "width:"+w+"<br>height:"+h; 

};


// Add a datalist object to the HTML Document so we can suggest pricing, subject to browser support.
jQuery( 'document' ).ready(function() {
  var parent = document.head;

  var newdl = document.createElement ('datalist');
	newdl.id = "SggPrices";
	newdl.innerHTML =       '  <option value="0.05">'+
  '  <option value="0.09">'+
  '  <option value="0.49">'+
  '  <option value="0.99">'+
  '  <option value="1.00">'+
  '  <option value="1.49">'+
  '  <option value="4.99">'+
  '  <option value="5.99">'+
  '  <option value="9.99">';

  parent.appendChild ( newdl);


  switch (location.pathname.substring(1)){
	case "woowrequest":
	  document.getElementsByClassName('avfr-filter')[0].style.display = "none";
	  document.getElementsByClassName('avfr-layout-main')[0].style.display = "none";
	  document.getElementsByClassName('user-votes-shortcode')[0].style.display = "none";
	  break;
  } 
  
});