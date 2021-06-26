
jQuery(document).ready(function () {
    var data = {
        action: 'hello',
        name: myPlugin.name
    };

    jQuery.get(myPlugin.ajaxurl, data, function (response) {
        jQuery('.history').append(response);
    });
});
function round(value, exp) {
    if (typeof exp === 'undefined' || +exp === 0)
      return Math.round(value);
  
    value = +value;
    exp = +exp;
  
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0))
      return NaN;
  
    // Shift
    value = value.toString().split('e');
    value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp)));
  
    // Shift back
    value = value.toString().split('e');
    return +(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp));
  }


jQuery(document).ready(function(){
    jQuery(".btn").click(function(){
        
        var amount=jQuery("#amount").val();
        var curr1='BTC';
        var curr2=jQuery("#currency-2 option:selected").text();
        var currRate1 =  document.getElementById('BTC').getAttribute('data-value');
        var currRate2 =  jQuery("#currency-2").val();
        var resultFull = (amount * currRate1) / currRate2;
        var resFullRound = round(resultFull, 4);
        jQuery('#history-log').prepend( '<span>' +curr1 + ' ' + amount+ ' ' + ' to ' +curr2 + ' = ' + resFullRound +'</br> </span>')

        jQuery.ajax({
            url:'/wp-content/plugins/cryprograph/insert.php',
            method:'POST',
            data:{
                amount:amount,
                curr1:curr1,
                curr2:curr2,
                resultFull:resultFull
            },
           success:function(data){
              
           }
        });
    });
});

