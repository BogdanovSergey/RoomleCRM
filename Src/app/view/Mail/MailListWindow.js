Ext.define('crm.view.Mail.MailListWindow', {
    extend  : 'Ext.window.Window',
    alias   : 'widget.MailListWindow',
    id      : "MailListWindow",
    title   : 'Список писем',
    autoShow  : true,
    autoScroll: true,
    height  : 600,
    width   : 900,
    layout  : 'fit',
    modal       : true,
    constrain   : true,
    closeAction : 'destroy',
    beforedestroy: function (window) {
        //Ext.getCmp('UsersGrid').close();
    },

    initComponent: function() {
        this.items = Ext.create('Ext.panel.Panel', {
            width : 600,
            header: false,
            //height: 300,
            layout : 'border',
            defaults : {
                padding: '0'
            },

            items  : [{
                xtype : 'panel',
                header: false,
                maxHeight: 250,
                region: 'north',
                autoHeight: true,
                autoScroll  : true,
                items: Ext.create('crm.view.Mail.MailGrid')
                //html  : 'контент контент контент <br><br><br><br><br><br><br>кконтент контент контент <br><br><br><br><br><br><br>контент контент контент контент контент <br><br><br><br><br><br><br>контент контент ',
            },/*{
                //xtype: 'splitter',   // A splitter between the two child items
                //border: 1
                xtype : 'panel',
                header: false,
                maxHeight: 50,
                region: 'north',
                //autoHeight: true,
                //autoScroll  : true,
                items: [
                    {   region  : 'west',
                        html        : 'sss1'
                    },
                    {   region  : 'east',
                        html        : 'ss2s'
                    }
                ]
            },*/
                {
                    header  : false,
                    xtype       : 'panel',
                    autoHeight  : true,
                    autoScroll  : true,
                    region: 'center',
                    //maxHeight: 300,
                    //html  : 'контент контент контент <br><br><br><br><br><br><br>контент контент контент <br><br><br><br><br><br><br>контент контент контент <br><br><br><br><br><br><br>контент контент контент <br><br><br><br><br><br><br>контент контент контент <br><br><br><br><br><br><br>контент контент контент <br><br><br><br><br><br><br>',
                    items: [
                        {   //autoHeight: true,
                            //autoScroll  : true,
                            id          : 'MailListWindow_Body',
                            html        : ''
                        }
                    ]
                    /*tbar:[{
                        //header  : true,
                        //text    : '123<br>asdf',
                        //iconCls : 'x-tbar-loading',
                        //hidden  : false,
                        //html  : 'from: asdf@asdf.rr<br>to: qwer',
                        handler : function() {
                            //Ext.getCmp('MailGrid').getStore().load(); // обновляем список
                        }
                    }]*/
                }
            ]

        });
        //Ext.widget('crm.view.UsersGrid');
        this.callParent(arguments);
    }


});