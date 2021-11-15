Ext.define('crm.view.Mail.MailListWindow', {
    extend  : 'Ext.window.Window',
    alias   : 'widget.MailListWindow',
    id      : "MailListWindow",
    title   : 'Список писем',
    autoShow  : true,
    autoScroll: true,
    height  : 400,
    width   : 900,
    layout  : 'fit',
    modal       : true,
    constrain   : false,
    closeAction : 'destroy',
    beforedestroy: function (window) {
        //Ext.getCmp('UsersGrid').close();
    },

    initComponent: function() {
        this.items = [
            {   id: 'MailListWindow_Container',
                region: 'center',
                //margin: '35 5 5 0',
                //layout: 'column',
                flex    : 1,
                //autoScroll: true,
                defaultType: 'container',
                autoHeight: true,
                /*border: 5,//
                style: {
                    borderColor: 'red',
                    borderStyle: 'solid'
                },/*/

                items: [
                    Ext.create('crm.view.Mail.MailGrid'),
                    {
                        border: 0,
                        columnWidth: 3/5,
                        //maxHeight: 300,
                        //padding: '5 0 5 5',
                        items: [
                            {   autoHeight: true,
                                autoScroll  : true,
                                id          : 'MailListWindow_Body',
                                html        : 'Ext.example.shortBogusMarkup'
                            }
                        ]
                    }
                ],
                listeners: {
                    'resize': function () {
                        /*var ContainerHeight = Ext.getCmp('MailListWindow_Container').getHeight() - 2;
                        Ext.getCmp('MailListWindow_List').setHeight( ContainerHeight );
                        Ext.getCmp('MailListWindow_Body').setHeight( ContainerHeight );*/
                    }
                }
            }


            /*Ext.create('Ext.panel.Panel', {
                flex    : 1,
                style: {
                    "margin-left": '0px',
                    "float": 'left'
                },
                //id: 'panel3',
                //frame: true,
                layout: {
                    type: 'table',
                    columns: 2,
                    tdAttrs: {
                        style: 'padding:2px'
                    }
                },
                defaultType: 'button',
                items: [
                    Ext.create('crm.view.Mail.MailGrid'),
                    {
                        xtype : 'panel',
                        width : 200,
                        border: 5,
                        style: {
                            borderColor: 'red',
                            borderStyle: 'solid'
                        }
                    }
                ],
                header: false
            }) */




            /*
            Ext.create('crm.view.Mail.MailGrid'),
            {
                xtype : 'panel',
                width : 200,
                border: 5,
                style: {
                    borderColor: 'red',
                    borderStyle: 'solid'
                }
            }*/
        ];
        //Ext.widget('crm.view.UsersGrid');
        this.callParent(arguments);
    }


});