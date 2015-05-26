var ServerMgmt = function() {
   var _public = {};

   _public.SambaController = {};
   _public.BackupController = {};
   _public.common = {};

   return _public;
}();



ServerMgmt.SambaController.changeRight = function(input) {


  console.debug(input.id);


  $.ajax({
    dataType: "json",
    url: getCustomerProjectUrl + e.val,
    success: function(jsonData) {
        $('#form_data [name=project] option').remove();
        $('#form_data [name=project]').append("<option></option>");
        for (var i = jsonData.length - 1; i >= 0; i--) {
            $('#form_data [name=project]').append('<option value="' + jsonData[i].id + '">' + jsonData[i].text + '</option>');
        };

        $('#form_data [name=project]').select2({ allowClear: true });
    }
  });

};
