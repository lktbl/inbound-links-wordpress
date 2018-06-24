var show_chart = function(customData, customTags){
  var ctx = document.getElementById("general_stats").getContext('2d');
  var data = {
    datasets: [{
      data: customData,
      backgroundColor: [
        'rgba(255, 99, 132, 0.8)',
        'rgba(54, 162, 235, 0.8)',
        'rgba(255, 206, 86, 0.8)',
        'rgba(75, 192, 192, 0.8)',
        'rgba(153, 102, 255, 0.8)',
        'rgba(255, 159, 64, 0.8)',
        'rgba(255, 150, 132, 0.8)',
        'rgba(54, 162, 23, 0.8)',
        'rgba(255, 20, 86, 0.8)',
        'rgba(145, 192, 192, 0.8)',
        'rgba(153, 23, 255, 0.8)'
      ],
      borderWidth: 0,
      hoverBorderWidth: 3,
    }],
    labels: customTags,

  };


  var incoming_traffic = new Chart(ctx, {

      type: 'doughnut',
      data: data,
      options: {
        title: {
          display: false,
        },
        legend: {
          display: true,
          position: 'bottom'
        },
      }
  });
}

jQuery(document).ready(function($){
  // Change page if new parameter is selected
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
  });

  if ($('#inboundlinks_stats').length) {
    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      dataType: "text",
      cache: false,
      data: {
        action : 'inboundlinks_get_data',
        parameter : $('#select_parameter').val()
      },
      success: function(response){

        var customData = [];
        var customTags = [];

        try {
            response = JSON.parse(response);
        }
        catch (err) {
          return true;
        }

        for(element in response){
          customData.push(response[element])
          customTags.push(element)
          $('#stats_table').append('<tr><td>'+element+'</td><td>'+response[element]+'</td></tr>')
        }
        show_chart(customData, customTags);
      }
    });
  }


});
