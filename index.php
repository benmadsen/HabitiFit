<!DOCTYPE html>
<html>
    <head>
        <title>FitBitica-1.1</title>
       <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    </head>
    
    <body>
        <script>
           $(document).ready(function() {
               
               //Data we need:
               //Fitbit Implicit Grant data: (scope, user_id, token_type, auth_token)
               //Habitica Api data (user_id, api_token, task_name)
               //TO ADD:  Check if this data is already in persistent storage.  If so, don't need to have has in url to work!
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
                   
                   //Set activity variables
                   var steps_taken = 0;
                   var calories_burned = 0;
                   var miles_walked = 0;
                   
                   $('#get-data').click(function(){
                       get_data();
                   });
                   
                    $("#habitica_info_submit").click(function( event ) {
                      localStorage.hab_user_id  = $('#user_id').val();
                      localStorage.hab_api_tok = $('#api_token').val();
                      localStorage.task_name = $('#task_name').val();
                      
                      //update_habitica_task(task_name, 'up', hab_user_id, hab_api_tok);
                        
                      $('#hab_output').html('api info updated');
                      $('#hab_output').fadeIn();
                      $('#hab_output').fadeOut(4000);
                        
                      event.preventDefault();
                    });
                   
                   //put in some logic here
                   //initialize times updated variable it if isn't already
                   if(!localStorage.times_updated){
                       localStorage.times_updated = 0;
                   }
                   
                   localStorage.today_date = get_today_date();
                   
                   window.setInterval(function(){
                       get_data();
                    }, 200000)
                   
                   function get_data(){
                       $.ajax({
                        url:'data.php',
                        dataType: 'json',
                        data: {token_type : localStorage.token_type, access_token : localStorage.auth_token, user_id : localStorage.user_id, date: localStorage.today_date},
                        async: false,
                        success: function(data){
                            console.log(data);
                            steps_taken = data.summary.steps;
                            calories_burned = data.summary.caloriesOut;
                            miles_walked = data.summary.distances[0].distance;
                            
                            times = Math.floor(steps_taken/500);
                            if(times > localStorage.times_updated){
                                if(localStorage.hab_user_id && localStorage.hab_api_tok){
                                    diff = times - localStorage.times_updated;
                                    for(i = 0; i< diff; i++){
                                         update_habitica_task(localStorage.task_name, 'up', localStorage.hab_user_id, localStorage.hab_api_tok);
                                    }
                                    localStorage.times_updated = times;
                                }else{
                                    $('#hab_output').html('please add habitica info');
                                    $('#hab_output').fadeIn();
                                    $('#hab_output').fadeOut(4000);
                                }
                            }
                            
                            $('#steps').html(steps_taken);
                            $('#calories').html(calories_burned);
                            $('#miles').html(miles_walked);
                        }
                       });
                   }
                   
                   //
                   // Updates habitica habit
                   //
                   
                   function update_habitica_task(task_name, direction, uid, tok){
                       $.ajax({
                        url:'habit_data.php',
                        data: {task_name: task_name, direction: direction, user_id: uid, api_tok: tok},
                        async: false,
                        success: function(data){
                            console.log(data);
                            if(data == 'ERROR'){
                                $('#hab_output').html('Could not update habitica data!');
                                $('#hab_output').fadeIn();
                                $('#hab_output').fadeOut(4000);
                            }
                            
                            $('#user_id').val('');
                            $('#api_token').val('');
                        }
                       });
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

                        today = yyyy+'/'+mm+'/'+dd
                        return today;
                   }
               }
           });
        </script>
        <p id="test"></p>
        <br>
        <a href="https://www.fitbit.com/oauth2/authorize?response_type=token&client_id=229TR9&scope=activity&prompt=none">Click Me!</a>
        <br><br>
        <a id="get-data">Get data</a>
        <br><br>
        <p>Steps Today: <span id="steps"></span></p>
         <p >Calories Out Today: <span id="calories"></span></p>
          <p>Miles Walked Today: <span id="miles"></span></p>
          <br>
          <hr>
          <br>
               Habitica User Id:<br>
               <input  type="text" id="user_id"/>
               <br>
              Habitica Api token:<br>
              <input name="searchTxt" type="password" id="api_token"/><br>
               Habitica Habit Name (to update):<br>
               <input  type="text" id="task_name"/>
              <h5 class="btn btn-default" id="habitica_info_submit">Submit</h5>
              <br>
          <h3 id="hab_output" style="display:none"></h3>
    </body>
</html>