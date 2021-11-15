Ext.define('crm.view.Settings.SettingsForm', {
    extend      : 'Ext.form.Panel',
    header      : false,
    autoScroll  : true,
    alias       : 'widget.SettingsForm',
    title       : 'Настройки',
    url         : 'Super.php',
    bodyPadding : 10,
    //id          : "ObjectForm",
    defaultType : 'textfield',
    initComponent: function() {
        //GlobVars.RegionOrCityUpdated = 0; // маркер: адрес не редактировался #AvitoAltAddr
        Ext.apply(this, {
            id          : "SettingsForm",
            items   : [
                {
                    xtype: 'hiddenfield',
                    name: 'Action',
                    value: 'SaveSettingsForm'
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'Название компании',
                    labelWidth : 160,
                    layout     : 'hbox',
                    items: [
                        {
                            allowBlank  : true,
                            name        : 'CompanyName',
                            xtype       : 'textfield',
                            emptyText   : 'Агентство недвижимости "Раз, два, три, продано!"',
                            width       : 400
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'Городской номер компании (обязателен для некоторых баз)',
                    labelWidth : 160,
                    layout     : 'hbox',
                    items: [
                        {
                            allowBlank : true,
                            name       : 'NavigatorXmlCompanyPhone',
                            xtype      : 'textfield',
                            vtype      : 'DigitsVtype',
                            emptyText  : '84951234567',
                            width      : 400
                        }
                    ]
                },

                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'Ссылка для импорта (обновление каждые 5 мин.)',
                    labelWidth : 160,
                    layout     : 'hbox',
                    items: [
                        {
                            allowBlank  : true,
                            name        : 'ImportObjectsUrl',
                            xtype       : 'textfield',
                            emptyText  : 'http://',
                            width       : 400
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'Прикрепление клиента к объекту',
                    defaultType: 'radiofield',
                    labelWidth : 160,
                    width      : 400,
                    defaults   : {
                        flex   : 1
                    },
                    layout: 'hbox',
                    items: [
                        {
                            boxLabel  : 'Обязательно',
                            name      : 'AttachClientToObjectStrict',
                            inputValue: '1',
                            itemId    : 'AttachClientToObjectStrictItm',
                            checked   : true
                        }, {
                            boxLabel  : 'Не обязательно',
                            name      : 'AttachClientToObjectStrict',
                            inputValue: '0',
                            itemId    : 'AttachClientToObjectItm'
                        }
                    ]
                }
            ],
            buttons : [
                {
                    text   : 'Сохранить',
                    itemId : 'ObjectFormSaveBtn',
                    handler: function() {
                        SubmitSettingsForm(false);
                        /*var SaveButtons = new Array('ObjectFormSaveBtn','ObjectFormSaveAndCloseBtn'); // Список кнопок для блокировки
                         if ( CheckObjectFormFields('city') == false ) { // если поля заполнены неправильно, выходим из сохранения
                         return;
                         }
                         TriggerSaveButtons('disable', SaveButtons );    // закрываем кнопки от двойного клика
                         CheckAvitoCompatible();                         // проверка и изменение формы для авито #AvitoAltAddr
                         Op_ExecAfterWork(                               // ждем завершения ajax запросов в предидущих ф-ях и сохраняем форму
                         function() {
                         SubmitObjectForm('ObjectWindow', 'ObjectTabs', 'ObjectForm', 'ObjectsGrid', 'ObjectAdditionsForm', false);
                         TriggerSaveButtons('enable', SaveButtons );
                         }
                         );*/
                    }
                },
                {
                    text    : 'Сохранить и закрыть',
                    itemId  : 'ObjectFormSaveAndCloseBtn',
                    handler : function() {
                        SubmitSettingsForm(true);
                        /*var NeedToClose = true; // Закрытие окна
                         var SaveButtons = new Array('ObjectFormSaveBtn','ObjectFormSaveAndCloseBtn'); // Список кнопок для блокировки
                         if ( CheckObjectFormFields('city') == false ) { // если поля заполнены неправильно, выходим из сохранения
                         return;
                         }
                         TriggerSaveButtons('disable', SaveButtons );    // закрываем кнопки от двойного клика
                         CheckAvitoCompatible();                         // проверка и изменение формы для авито #AvitoAltAddr
                         Op_ExecAfterWork(                               // ждем завершения ajax запросов в предидущих ф-ях и сохраняем форму
                         function() {
                         SubmitObjectForm('ObjectWindow', 'ObjectTabs', 'ObjectForm', 'ObjectsGrid', 'ObjectAdditionsForm', NeedToClose);
                         TriggerSaveButtons('enable', SaveButtons );
                         }
                         );*/
                    }
                },
                /*{/
                    text    : 'Загрузка данных',
                    hidden  : true,
                    handler : function() {
                        Ext.getCmp('ObjectForm').getForm().load({
                            waitMsg:'Идет Загрузка...',
                            url: 'Super.php',
                            method: 'GET',
                            params: {
                                id     : 10,
                                Action : 'OpenObject'},
                            success: function(response, options) {
                            }
                        });
                    }
                },*/
                {
                    text: 'Закрыть',
                    handler: function() {
                        //ObjectForm.getForm().reset();
                        Ext.getCmp('SettingsWindow').close();
                    }
                }]

        } );
        this.callParent(arguments);
    },
    listeners: {
        close : {
            fn: function() {
                alert(1222);
            },
            element: 'body'
        }
    }

});
