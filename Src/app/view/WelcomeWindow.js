Ext.define('crm.view.WelcomeWindow' ,{
    extend  : 'Ext.window.Window',
    alias   : 'widget.WelcomeWindow',
    id      : "WelcomeWindow",
    title   : 'Окно приветствия',
    autoShow: true,
    autoScroll: true,
    height  : 500,
    width   : 600,
    maxWidth: 600,
//    layout  : 'fit',
    modal       : true,
    constrain   : true,
    closeAction : 'destroy',
    layout : 'anchor',
    beforedestroy: function (window) {
        //Ext.getCmp('UsersGrid').close();
    },

    initComponent: function() {
        this.items = [
            Ext.create('Ext.panel.Panel', {
                id : 'WelcomeWindowPanel',
                //title: 'Table Layout',
                //width: 300,
                //height: 150,
                anchor  : '100% 100%',
                layout : 'anchor',
                autoScroll  : true,
                header      : false,
                flex        : true,//
                //autoHeight  : true,
                //layout: {
                    //type: 'table',
                    //align : 'center',
//                    type: 'vbox',
                    // The total column count must be specified here
//                    columns: 4
                //},
                items: [
                   // {
                        //layout: column

                        //items: [
                            Ext.create('Ext.Img', {
                                title       : 'Нажмите чтобы закрыть',
                                id          : "WelcomeWindowImage",
                                autoRender  : true,
                                src         : 'images/Background/MoscowCitySky2.jpg',
                                anchor      : '100% 50%',
                                listeners: {
                                    click: {
                                        element : 'el', //bind to the underlying el property on the panel
                                        fn      : function() {
                                            Ext.getCmp('WelcomeWindow').close();
                                        }
                                    }
                                }
                            }),
                        //    ] Ext.getCmp('iiiii').render();
                    //},
        /*{
            html: '<b>Порталы доступные для выгрузки рекламы:</b>',
            border: false,
            hidden: true
//                        colspan: 4
            //rowspan: 2
        },
        Ext.create('Ext.panel.Panel', {
            id : 'WelcomeWindowPanelPortalItems',
            border : false,
            //bodyStyle: 'padding:5px',
            padding     : '10 10 10 10',
            //frame       : true,
            html    : '<img src="images/spinner.gif" width="30">',
            layout: {
                //align : 'center',
//                            type: 'vbox'
                // The total column count must be specified here
                //columns: 4
            }
        }),
        {
            border  : false,
            itemId  : 'WelcomeWindowPanelPortalDescription'
        },
        {
            border  : false,
            itemId  : 'WelcomeWindowPanelPortalPrice'
        },
        {
            border  : false,
            itemId  : 'WelcomeWindowPanelPortalXmlLink'
        },
        {
            border  : false,
            itemId  : 'WelcomeWindowPanelPortalLoadCount'
        },
        {
            border  : false,
            itemId  : 'WelcomeWindowPanelPortalLoadInfoText'
        },
        {
            border  : false,
            itemId  : 'WelcomeWindowPanelPortalTechInfoText'
        },
        {
            border  : false,
            itemId  : 'WelcomeWindowPanelPortalContacts'
        },
                    {
                        html: '<b>Последние новости:</b>',
                        border: false
                    },*/
                    {
                        border  : false,
                        itemId  : 'WelcomeWindowNewsPanel',
                        html    : '<img src="images/spinner.gif" width="30">'
                    },
                    /*{
                        html: '<b>Оплата:</b>',
                        border: false
                    },*/
                    {
                        border  : false,
                        hidden  : true,
                        itemId  : 'WelcomeWindowBillingPanel',
                        html    : '<img src="images/spinner.gif" width="30">'
                    }

                ],
                renderTo: Ext.getBody()

            })
        ];
        this.buttons = [
            {
                text: 'Платежные реквизиты',
                handler: function() {
                    Ext.Msg.show({
                        title   : Words_PaymentTitle,
                        msg     : Words_PaymentMsg,
                        buttons : Ext.Msg.OK,
                        icon    : Ext.Msg.INFO
                    });
                }
            },
            {
                text: 'Закрыть',
                handler: function() {
                    Ext.getCmp('WelcomeWindow').close();
                }
            }
        ];

        this.callParent(arguments);
    }
    //  событие "afterrender: WelcomeWindowPanelRender()" см. в Controller.js

    /*listeners: {
        afterrender: {
            element: 'el', //bind to the underlying el property on the panel
            fn: function() {
                // загружаем информацию о доступных порталах в форму
                //WelcomeWindowPanelRender();
            }
        }
    }*/

});