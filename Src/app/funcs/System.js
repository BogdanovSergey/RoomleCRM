
    function LogOut() {
        //Ext.util.Cookies.clear('sess');

        Ext.Ajax.request({
            url: 'Super.php',
            params: {
                Action : "UserLogout"
            },
            success: function(response, opts) {
                var obj = Ext.decode(response.responseText);
                if(obj.success == true) {
                    console.log('успешно выходим...');
                } else {
                    Ext.util.Cookies.clear('sess');
                    document.location.href = MainSiteUrl;
                    //alert(Words_SystemErrorMsg + '111' + '\n' + Words_CallProgrammerMsg + '\n\n' + obj.message);
                }
            },
            failure: function(response, opts) {
                Ext.util.Cookies.clear('sess');
                document.location.href = MainSiteUrl;
                //alert('Logout error :-(' + '\n' + Words_CallProgrammerMsg);
            }
        });


    }

    function CountObectLength(obj) {
        var count = 0;
        var i;
        for (i in obj) {
            if (obj.hasOwnProperty(i)) {
                count++;
            }
        }
        return count;
    }

