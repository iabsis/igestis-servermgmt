var ServerMgmt = function() {
   var _public = {};

   _public.SambaController = {};
   _public.BackupController = {};
   _public.common = {};

   return _public;
}();



ServerMgmt.SambaController.changeRight = function(input) {


  console.debug($(input).attr('data-login'));
  console.debug($(input).attr('data-folder'));
  console.debug($(input).attr('checked'));

  login = $(input).attr('data-login');
  folder = $(input).attr('data-folder');
  // Do the condition here
  if ($(input).attr('checked')) {
    right = $(input).attr('data-right');
  } else {
    right = "none";
  }

  $.ajax({
    dataType: "json",
    type: "GET",
    url: "http://igestis3/igestis/index.php",
    data: "Module=serverMgmt&Action=samba_change_right&folderName="+folder+"&employeeAccount="+login+"&right="+right,
    success: function(data) {
      if(data.hasOwnProperty('error')) {
        $(input).removeAttr('checked');
        bootbox.alert(data.error);
      } else {
         if ($(input).attr('data-right') == "write") {
           if ($(input).attr('checked') == "checked") {
             $(input).parent().parent().children().each(function() {
               if ($(this).children().attr('data-right') == "read") {
                 $(this).children().attr('checked','checked');
                 $(this).children().attr('disabled','true');
               }
             });
           } else {
             $(input).parent().parent().children().each(function() {
               if ($(this).children().attr('data-right') == "read") {
                 $(this).children().removeAttr('checked');
                 $(this).children().removeAttr('disabled');
               }
             });
           }
         }
       }
    },
    error: function(e) {
      bootbox.alert("An unknown error has occured");
      //console.log(e.message);
   }
  });

};
