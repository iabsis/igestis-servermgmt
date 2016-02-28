var ServerMgmt = function() {
   var _public = {};

   _public.SambaController = {};
   _public.BackupController = {};
   _public.common = {};
   _public.init = function() {};

   return _public;
}();

ServerMgmt.init = function(options) {
    $(function() {
        /**
         * jQUerySelector to represent the line of the table currently edited
         * @type {jQyer}
         */
        var currentEditedLine = null;

        /**
         * Click event of "rename" buttons
         */
        $("body").on("click", "[data-rename-folder]", function() {
            $("#id-previousName").val($(this).data("renameFolder"));
            $("#id-newName").val($(this).data("renameFolder"));
            currentEditedLine = $(this).closest("tr");
        });

        $("body").on("click", "[data-delete-folder]", function() {
            $("#id-previousName").val($(this).data("renameFolder"));
            $("#id-newName").val($(this).data("renameFolder"));
            var self = $(this);
            bootbox.confirm("Are you sure that you want to delete this folder ?", "No", "Yes", function(result) {
                if(result) window.location.href= options.deleteUrl + self.closest("tr").find(".folder-name").text();
            });
        });

        

        /**
         * Event when validating the rename form
         */
        $("#rename-folder-form").on("submit", function (e) {
            e.preventDefault();
            var ajaxUrl = $(this).attr("action");
            var newName = $("#id-newName").val();

            // Sending the form in post format and parse the returned json
            $.ajax({
                dataType: "json",
                type: "POST",
                url: ajaxUrl,
                data: $(this).serialize(),
                success: function(jsonData) {
                    igestisParseJsonAjaxResult(jsonData);
                    // ON success, we update the new folder name in the table ...
                    currentEditedLine.find(".folder-name").text(newName);
                    // ... and in the data-rename-folder attribute
                    currentEditedLine.find("[data-rename-folder]").data("renameFolder", newName);
                },
                error: function () {
                    igestisWizz("An error has occured while trying to rename the folder", "WIZZ_ERROR", "#id-wizz", false);
                },
                "complete": function() {
                    $("#rename-folder-modal").modal("hide");
                }
            });
        });

    });
}

ServerMgmt.SambaController.changeRight = function(input) {


  login = $(input).attr('data-login');
  folder = $(input).attr('data-folder');

  if ($(input).attr('checked')) {
    right = $(input).attr('data-right');
  } else {
    right = "none";
  }

  $.ajax({
    dataType: "json",
    type: "GET",
    url: "index.php",
    data: "Module=serverMgmt&Action=samba_change_right&folderName="+folder+"&employeeAccount="+login+"&right="+right,
    success: function(data) {
      if(data.hasOwnProperty('error')) {
        $(input).removeAttr('checked');
        bootbox.alert(data.error);
        igestisWizz("An error has occured while changing the right", "WIZZ_ERROR", "#id-wizz", false);
      } else {
        igestisWizz("Right changed successfully", "WIZZ_SUCCESS", "#id-wizz", false);
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
