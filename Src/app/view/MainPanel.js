Ext.define('crm.view.MainPanel', {
    extend  : 'Ext.form.Panel',
    alias   : 'widget.MainPanel',
    height  : 30,
    //width   : 500,
    /*border : 10,
    style: {
        borderColor: 'red',
        borderStyle: 'solid'
    },*/
    items   : [
        Ext.create('Ext.Button', {
            //id      : 'Buttons',
            //itemId  : 'Btn_Users',
            iconCls : 'LayoutCls',
            //text    : 'Сотрудники',
            disabled: false,
            handler : function() {
                crm.view.WelcomeWindow.create({});
            }
        }),
        {
            xtype   : 'text',
            width   : 5,
            text    : ' '
        },
        Ext.create('Ext.Button', {    // менюшка выбора типа объектов
            iconCls   : 'ObjectsCls',
            text      : 'Объекты',
            //id        : 'ObjectsBtn',
            //renderTo  : Ext.getBody(),
            arrowAlign: 'right',
            menu      : [
                {text: 'Городская недвижимость',
                    listeners: {
                        click: {
                            fn: function() {
                                OpenRealtyGrid('city');
                            }
                        }
                    }
                },
                {
                    text: 'Загородная недвижимость',
                    listeners: {
                        click: {
                            //element: 'el', //bind to the underlying el property on the panel
                            fn: function() {
                                OpenRealtyGrid('country');

                            }
                        }
                    }
                },
                {
                    text: 'Коммерческая недвижимость',
                    listeners: {
                        click: {
                            //element: 'el', //bind to the underlying el property on the panel
                            fn: function() {
                                OpenRealtyGrid('commerce');

                            }
                        }
                    }
                }
            ]

        }),
        //{ xtype: 'splitter' },

        {xtype:'text',width:5,text:' '},

        Ext.create('Ext.Button', {
            itemId  : 'Btn_Clients',
            iconCls : 'ClientsCls',
            text    : 'Клиенты',
            //disabled: true,
            handler : function() {
                crm.view.ClientsListWindow.create({});
            }
        }),

        {xtype:'text',width:5,text:' '},

        Ext.create('Ext.Button', {
            //id      : 'Buttons',
            itemId  : 'Btn_Users',
            iconCls : 'UserCls',
            text    : 'Сотрудники',
            disabled: true,
            handler : function() {
                crm.view.UsersListWindow.create({});
                //Ext.widget("UsersListWindow").create();
            }
        }),

        {xtype:'text',width:5,text:' '},

        Ext.create('Ext.Button', {
            //id      : 'Buttons',
            itemId  : 'Btn_Deals',
            iconCls : 'DealsCls',
            text    : 'Сделки',
            disabled: true,
            handler : function() {
                //crm.view.UsersListWindow.create({});
                //Ext.widget("UsersListWindow").create();
            }
        }),

        {xtype:'text',width:5,text:' '},

        Ext.create('Ext.Button', {
            //id      : 'Buttons',
            iconCls : 'EmailCls',
            text    : 'Почта',
            handler : function() {
                crm.view.Mail.MailListWindow.create({});
                //Ext.widget("UsersListWindow").create();
            }
        }),

        {xtype:'text',width:5,text:' '},

        Ext.create('Ext.Button', {
            itemId  : 'Btn_Sobs',
            iconCls : 'OwnersCls',
            text    : 'Собственники',
            disabled: true,
            handler : function() {
                OpenOwnersGrid();
            }
        }),

        {xtype:'text',width:5,text:' '},

        Ext.create('Ext.Button', {
            itemId  : 'Btn_Reports',
            iconCls : 'ReportsCls',
            text    : 'Отчеты',
            disabled: true,
            handler : function() {

            }
        }),
        {xtype:'text',width:5,text:' '},

        Ext.create('Ext.Button', {
            iconCls : 'SettingsCls',
            text    : 'Настройки',
            disabled: false,
            menu      : [
                {   itemId   : 'Btn_SysSettings',
                    text     : 'Настройки системы',
                    disabled : true,
                    listeners: {
                        click: {
                            fn: function() {
                                crm.view.Settings.SettingsWindow.create({});
                            }
                        }
                    }
                },
                {   itemId   : 'Btn_AdPrices',
                    text     : 'Настройки рекламы',
                    disabled : true,
                    listeners: {
                        click: {
                            fn: function() {
                                crm.view.Settings.AdPricesWindow.create({});
                            }
                        }
                    }
                },
                {   itemId   : 'Btn_StructureRights',
                    text     : 'Структура компании, права доступа',
                    disabled : true,
                    listeners: {
                        click: {
                            fn: function() {
                                crm.view.Settings.StructureWindow.create({});
                            }
                        }
                    }
                }
            ]
        }),

        {
            xtype   : 'text',
            width   : 150,
            text    : ' '
        },
        {
            xtype   : 'text',
            itemId  : 'Lbl_LoggedUserName',
            cls     : 'LoggedInCls',
            text    : ' '
        },
        {
            xtype   : 'text',
            width   : 20,
            text    : ' '
        },
        {
            xtype   : 'text',
            cls     : 'PhoneCls',
            text    : 'Поддержка: +7(903)124-55-31'
        },
        {
            xtype   : 'text',
            width   : 20,
            text    : ' '
        },
        Ext.create('Ext.Button', {
            //id      : 'Buttons',
            iconCls : 'LogOutCls',
            //text    : '',
            //inputAttrTpl: " data-qtip='фывафыва' ", // TODO сделать всплывающий текст над кнопкой
            handler : function() {
                LogOut();
            }
        })

        //'-',
        /*{
            xtype: 'text',
            text: 'right align needed!'
        }*/
    ]
});





