jQuery(document).ready(function($){
  $('#select_parameter').on('change', function(){
    var parameter = $(this).val();

    // Change url parameter
    // Code from: https://samaxes.com/2011/09/change-url-parameters-with-jquery/
    var queryParameters = {}, queryString = location.search.substring(1);
    var re = /([^&=]+)=([^&]*)/g;
    var m;
    while (m = re.exec(queryString)) {
        queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
    }
    queryParameters['parameter'] = parameter;
    queryParameters['parameter'] = parameter;
    location.search = $.param(queryParameters);
  })
});
