Ext.define('crm.view.ObjectHistory', {
    extend      : 'Ext.form.Panel',
    id          : "ObjectHistory",
    alias       : 'widget.ObjectHistory',
    url         : 'Super.php',
    disabled    : false,
    autoScroll  : true,
    collapsible : true,
    frame       : true,
    //maxHeight   : 900,
    title       : 'История',
    initComponent: function() {
        Ext.apply(this, {
            id          : "ObjectHistory",
            items: [
                {

                    header  : false,
                    xtype       : 'panel',
                    autoHeight  : true,
                    autoScroll  : true,
                    region: 'center',
                    itemId  : 'ObjectHistoryText',
                    html    : 'asd<b>dff'
                }
            ],
            buttons : [
                {
                    text: 'Закрыть',
                    handler: function() {
                        //ObjectForm.getForm().reset();
                        Ext.getCmp('ObjectWindow').close();
                    }
                }]
        });

        this.callParent(arguments);
    },
    listeners: {
        show: {
            fn: function () {
                var ObjectId = Ext.ComponentQuery.query('#LoadedObjectId')[0].value;
                Ext.Ajax.request({
                    url : LoadObjectHistoryUrl,
                    params  : {
                        ObjectId: ObjectId,
                        Period  : 'all',
                        Format  : 'html'
                    },
                    success: function(response, opts) {
                        var obj = Ext.decode(response.responseText);
                        if(obj.success == true) {
                            var text = Ext.ComponentQuery.query('#ObjectHistoryText')[0];
                            text.update(obj.data);
                        }
                    },
                    failure: function(response, opts) {
                        alert('ошибка при LoadObjectHistory ');
                    }
                });
            }
        }
    }
});

