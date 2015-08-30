<!DOCTYPE html>
<html>
    <head>
        <title>FitBitica-1.1</title>
       <script src="js/jquery-2.1.4.min.js"></script>
       <script src="js/index.js"></script>
       <script src="js/bootstrap.min.js"></script>
       <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
       <link rel="stylesheet" href="css/style.css" type="text/css" />
    </head>
    
    <body>
      
        <nav class="navbar navbar-inverse navbar-static-top">
          <div class="container-fluid">
            <div class="navbar-header">
              <a class="navbar-brand" href="#">FitBitica</a>
            </div>
          </div>
        </nav>
      
      <div class="container">
          <div class="row">
              <a href="https://www.fitbit.com/oauth2/authorize?response_type=token&client_id=229TR9&scope=activity&prompt=none">Click Me!</a>
                <br><br>
                <a class="btn btn-default" id="get-data">Get data</a>
          </div>
      </div>
      
       <div class="container" id="main_view">
           <div class="row">
              <div class="col-md-4 fitbit-color">
                <br>
                <p>Steps Today: <span id="steps"></span></p>
                 <p >Calories Out Today: <span id="calories"></span></p>
                  <p>Miles Walked Today: <span id="miles"></span></p>
                  <br>
              </div>
              
              <div class="col-md-4 center-block">
                   <div class="form-group">
                      <h4 class="control-label">Pick Whichever Metrics You'd Like to Track!</h4>
                      <h5>You'll need to have a habit whose text is the same as the input form.</h5>
                      <h5 class="text-muted">i.e. "500 steps taken"</h5>
                      
                      <div class="input-group">
                        <span class="input-group-addon">
                            <input type="checkbox" aria-label="..." id="track_steps">
                        </span>
                        <input type="text" id="steps_value" class="form-control">
                        <span class="input-group-addon">steps taken</span>
                      </div>
                      <br>
                      
                      <div class="input-group">
                        <span class="input-group-addon">
                            <input type="checkbox" aria-label="..." id="track_miles">
                        </span>
                        <input type="text" id="miles_value" class="form-control">
                        <span class="input-group-addon">miles traveled</span>
                      </div>
                      <br>
                      
                      <div class="input-group">
                        <span class="input-group-addon active">
                            <input type="checkbox" aria-label="..." id="track_calories">
                        </span>
                        <input type="text" id="calories_value" class="form-control">
                        <span class="input-group-addon">calories burned</span>
                      </div>
                      
                      <h5 class="btn btn-default" id="update_submit">Submit</h5>
                    </div>
              </div> 
              
              <div class="col-md-4 habit-color">
                 <h3>Enter Habitica Data Here:</h3>
                  <div class="form-group">
                       <label class="control-label">User Id</label>
                       <input  class="form-control" type="text" id="user_id"/>
                       <br>
                       <label class="control-label">Api Token</label>
                      <input class="form-control" name="searchTxt" type="password" id="api_token"/><br>
                        <label class="control-label">Habit Name To Update</label>
                       <input class="form-control" type="text" id="task_name"/>
                      <h5 class="btn btn-default" id="habitica_info_submit">Submit</h5>
                      <br>
                      <h3 id="hab_output" style="display:none"></h3>
                  </div>
              </div>
              
                           
           </div>
       </div>
    </body>
</html>