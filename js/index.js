$(document).ready(function() {

   //Data we need:
   //Fitbit Implicit Grant data: (scope, user_id, token_type, auth_token)
   //Habitica Api data (user_id, api_token, task_name)
   //
   //TO ADD:  Check to see if Fitbit auth token has expired to present a new auth form!
   //TO ADD: error handling!


   //If window has hash, it means we've got some FitBit api data to crunch!
   if(window.location.hash){
       url = window.location.hash.substr(1).split('&');
       re = /=.*/;
       var data = [];
       url.forEach(function(item){
           data.push(re.exec(item).toString().substr(1));
       });

       //Get authentication data
       localStorage.scope = data[0];
       localStorage.user_id= data[1];
       localStorage.token_type = data[2];
       localStorage.auth_token = data[4];
   }
    
    //check to see if we have fitbit auth data before we execute
   if(localStorage.scope && localStorage.user_id && localStorage.token_type && localStorage.auth_token){
       //Set activity variables
       localStorage.steps_taken = 0;
       localStorage.calories_burned = 0;
       localStorage.miles_walked = 0;
       
       //setup habit tracking variables
       if(!localStorage.track){
           track = {cal: 0, mile: 0, step: 0};
           localStorage.track = JSON.stringify(track);
       }
       if(!localStorage.track_bool){
           track = {cal: false, mile: false, step: false};
           localStorage.track_bool= JSON.stringify(track);
       }
       
       
       $('#get-data').click(function(){
           get_data();
       });

        $("#habitica_info_submit").click(function( event ) {
          if($('#user_id').val()){
              localStorage.hab_user_id  = $('#user_id').val();
          }
          if($('#api_token').val()){
              localStorage.hab_api_tok = $('#api_token').val();
          }
          if($('#task_name').val()){
              localStorage.task_name = $('#task_name').val();
          }
            
          $('#hab_output').html('api info updated');
          $('#hab_output').fadeIn();
          $('#hab_output').fadeOut(4000);

          event.preventDefault();
        });
       
       //if tasks update is updated
       $("#update_submit").click(function(event){
          track = JSON.parse(localStorage.track); 
          track_bool = JSON.parse(localStorage.track_bool);
          track.cal, track.mile, track.step = 0;
          track_bool.cal, track_bool.mile,track_bool.step = false;
           
          if($("#track_calories").is(':checked') && $("#calories_value").val()){
             track.cal =  $("#calories_value").val();
             track_bool.cal = true;
          }
           
          if($("#track_steps").is(':checked') && $("#steps_value").val() ){
              track.step = $("#steps_value").val();
              track_bool.step = true;
          }
           
          if($("#track_miles").is(':checked') && $("#miles_value").val()){
              track.mile = $("#miles_value").val();
              track_bool.mile = true;
          }
           localStorage.track = JSON.stringify(track);
           localStorage.track_bool = JSON.stringify(track_bool);
           
           reset_checkboxes();
           
           event.preventDefault();
       });

       reset_checkboxes();
       
       //initialize some variables it they aren't already
       if(!localStorage.times_updated){
           localStorage.times_updated = 0;
       }
       if(!localStorage.today_date){
            localStorage.today_date = get_today_date();   
       }

       localStorage.fetch_date = get_today_date();
       
       if(new Date(localStorage.fetch_date).getTime() > new Date(localStorage.today_date).getTime()){
           localStorage.today_date = localStorage.fetch_date;
           console.log("It's a new day!");
           //reset counters
           localStorage.times_updated=0;
       }
       
       window.setInterval(function(){
           get_data();
        }, 180000)
       
   }else{
       $("#main_view").css("display","none");
   }
    
    
    function get_data(){
       $.ajax({
        url:'data.php',
        dataType: 'json',
        data: {token_type : localStorage.token_type, access_token : localStorage.auth_token, user_id : localStorage.user_id, date: localStorage.today_date},
        async: false,
        success: function(data){
            console.log(data);
            localStorage.steps_taken = data.summary.steps;
            localStorage.calories_burned = data.summary.caloriesOut;

            data.summary.distances.forEach(function(item){
                if(item.activity == "total"){
                    localStorage.miles_walked = item.distance;
                }
            });

            times = Math.floor(localStorage.steps_taken/500);
            if(times > localStorage.times_updated){
                if(localStorage.hab_user_id && localStorage.hab_api_tok){
                    diff = times - localStorage.times_updated;
                    for(i = 0; i< diff; i++){
                         if(update_habitica_task(localStorage.task_name, 'up', localStorage.hab_user_id, localStorage.hab_api_tok)){
                            localStorage.times_updated = times;
                         }
                    }

                }else{
                    $('#hab_output').html('please add habitica info');
                    $('#hab_output').fadeIn();
                    $('#hab_output').fadeOut(4000);
                }
            }

            $('#steps').html(localStorage.steps_taken);
            $('#calories').html(localStorage.calories_burned);
            $('#miles').html(localStorage.miles_walked);
        }
       });
   }
    
    //
    // Updates habitica habit
    //

    function update_habitica_task(task_name, direction, uid, tok){
       return_val = false;
       $.ajax({
        url:'habit_data.php',
        data: {task_name: task_name, direction: direction, user_id: uid, api_tok: tok},
        async: false,
        success: function(data){
            if(data == 'ERROR'){
               return_val = false;
                $('#hab_output').html('Could not update habitica data!');
                $('#hab_output').fadeIn();
                $('#hab_output').fadeOut(4000);
            }

            $('#user_id').val('');
            $('#api_token').val('');
            return_val = true;
        }
       });
       return return_val;
    }

    
    //keep checkboxs updated on page refresh
   //really clunky down here
   function reset_checkboxes(){
       if(JSON.parse(localStorage.track_bool)){
           track = JSON.parse(localStorage.track_bool);
               $("#track_calories").prop("checked",track.cal);
               $("#track_steps").prop("checked",track.step);
               $("#track_miles").prop("checked",track.mile);
       }
       if(JSON.parse(localStorage.track)){
           track = JSON.parse(localStorage.track);
               $("#calories_value").attr("placeholder", (track.cal) ? track.cal : "").val("").focus().blur();
               $("#steps_value").attr("placeholder", (track.step) ? track.step : "").val("").focus().blur();
               $("#miles_value").attr("placeholder", (track.mile) ? track.mile : "").val("").focus().blur();
       }
   }
    
    //
    // Gets today's date in the form of yyyy/mm/dd
    //
    function get_today_date(){
       var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();

        if(dd<10) {
            dd='0'+dd
        } 

        if(mm<10) {
            mm='0'+mm
        } 

        today = yyyy+'-'+mm+'-'+dd
        return today;
    }
});