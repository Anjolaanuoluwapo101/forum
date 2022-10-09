 <!-- Left container -->
        <div class="w3-hide-small w3-col m3 l4  w3-container">
          <button onclick="showAccordion('show1')" class="w3-btn w3-block w3-black w3-center"> Profile <i class="fa fa-profile"></i> </button>
          <div id="show1" class="w3-container w3-hide" style="width:100%">
            <a href="profile/profile.php" class="w3-button w3-block w3-left-align"> <i class="">&nbsp</i> Profile</a>
          <a href="" class="w3-button w3-block w3-left-align"> <i class="fa fa-bell">&nbsp</i>Notification</a>
            <p>
              
            </p>
          </div>
        <button onclick="showAccordion('show2')" class="w3-btn w3-block w3-black w3-center">Categories</button>
          <div id="show2" class="w3-container w3-hide" style="width:100%">
            <button onclick="showAccordion('show2a')" class="w3-btn w3-block w3-black w3-center"> Health Care <i class="fa fa-profile"></i> </button>
              <div id="show2a" class="w3-container w3-hide" >
               <a class="w3-button w3-tiny w3-center" style="width:100%;">Pharmacy</a>
              </div>
            <button onclick="showAccordion('show2b')" class="w3-btn w3-block w3-black w3-center"> Technology <i class="fa fa-profile"></i> </button>
              <div id="show2b" class="w3-container w3-hide ">
               <a class="w3-button  w3-tiny w3-center" style="width:100%;">Programming</a>
              </div>
          </div>

          <div class="" style="" >
          <a class="w3-button w3-block w3-left-align ">Categories</a>
          <a class="w3-button w3-block w3-left-align" >Some Text</a>
          <a class="w3-button w3-block w3-left-align">About</a>
          </div>
          <!--End of left container-->
        </div>
        
        <!-- Container for small screen sized phones-->
          <div class="w3-container w3-animate-left w3-medium-hide w3-large-hide" id="leftContainer" style="position:fixed;z-index:1000;top:0;display:none;width:80%;height:100%;background-color:white!important">
          <button onclick="showAccordion('show1mobile')" class="w3-btn w3-block w3-black w3-center"> Profile <i class="fa fa-profile"></i> </button>
          <div id="show1mobile" class="w3-container w3-hide" style="width:100%">
            <a href="profile/profile.php" class="w3-button w3-block w3-left-align"> <i class="">&nbsp</i>Profile</a>
            <a href="" class="w3-button w3-block w3-left-align"> <i class="fa fa-bell">&nbsp</i>Notification </a>
            <p>
              
            </p>
          </div>
          <br>
        <button onclick="showAccordion('show2mobile')" class="w3-btn w3-block w3-black w3-center">Categories</button>
          <div id="show2mobile" class="w3-container w3-hide" style="width:100%">
            <br>
            <button onclick="showAccordion('show2amobile')" class="w3-btn w3-block w3-black w3-center"> Health Care <i class="fa fa-profile"></i> </button>
              <div id="show2amobile" class="w3-container w3-hide" >
               <a class="w3-button w3-tiny w3-center" style="width:100%;">Pharmacy</a>
              </div>
            <button onclick="showAccordion('show2bmobile')" class="w3-btn w3-block w3-black w3-center"> Technology <i class="fa fa-profile"></i> </button>
              <div id="show2bmobile" class="w3-container w3-hide ">
               <a class="w3-button  w3-tiny w3-center" style="width:100%;">Programming</a>
              </div>
          </div>

          <div class="" style="" >
          <a class="w3-button w3-left-align " style="width:100%">Categories</a>
          <a class="w3-button w3-left-align" style="width:100%">Some Text</a>
          <a class="w3-button w3-left-align" style="width:100%">About</a>
          </div>
          </div>
 