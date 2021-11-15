Ext.define('crm.view.ClientForm', {
    extend      : 'Ext.form.Panel',
    //alias       : 'widget.ClientForm',
    title       : 'Характеристики',
    header      : false,
    autoScroll  : true,
    url         : 'Super.php',
    bodyPadding : 10,
    id          : "ClientForm",
    defaultType : 'textfield',
    initComponent: function() {
        //GlobVars.RegionOrCityUpdated = 0; // маркер: адрес не редактировался #AvitoAltAddr
        var required = '<span style="color:red;" data-qtip="Обязательное поле">*</span>';
        Ext.apply(this, {
            id          : "ClientForm",
            //border: false, // почему не работает??
            items   : [
                // TODO удалить "id" внутри? itemId должно быть достаточно (file:///C:/Temp/ext-4.2.1-gpl/ext-4.2.1.883/docs/index.html#!/api/Ext.AbstractComponent-cfg-itemId)
                // заменить внешние обращения на: p1 = c.getComponent('p1');
                {
                    xtype   : 'hiddenfield',
                    name    : 'Action',
                    itemId  : 'Action',
                    value   : 'SaveClientForm'
                },
                {
                    xtype   : 'hiddenfield',
                    name    : 'EditSpecial',
                    itemId  : 'EditSpecial',
                    value   : '0' // 1 - если нужно сохранить только некоторые поля (не всю форму)
                },
                {
                    // поле по которому обновляется открытый объект
                    // при добавлении сюда добавляется новый id
                    xtype   : 'hiddenfield',
                    name    : 'LoadedClientId',
                    itemId  : 'LoadedClientId'
                },

                {
                    fieldLabel  : 'Имя',
                    emptyText   : 'Александр',
                    name        : 'FirstName',
                    allowBlank  : false,
                    blankText   : 'Необходимо заполнить поле',
                    afterLabelTextTpl: required,
                    xtype       : 'textfield',
                    width       : 400
                },
                {
                    fieldLabel  : 'Отчество',
                    emptyText   : 'Сергеевич',
                    name        : 'SurName',
                    allowBlank  : true,
                    width       : 400,
                    xtype       : 'textfield',
                    getSubmitValue: function() {
                        // "submitEmptyText:false" - не работает (баг Extjs'a), добавляем хак на каждое поле
                        var value = this.getValue();
                        if(Ext.isEmpty(value)) { return null; }
                        return value;
                    }
                },
                {
                    fieldLabel  : 'Фамилия',
                    emptyText   : 'Пушкин',
                    name        : 'LastName',
                    xtype       : 'textfield',
                    allowBlank  : true,
                    width       : 400,
                    getSubmitValue: function() {
                        // "submitEmptyText:false" - не работает (баг Extjs'a), добавляем хак на каждое поле
                        var value = this.getValue();
                        if(Ext.isEmpty(value)) { return null; }
                        return value;
                    }
                },

                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'Тип',
                    layout     : 'hbox',
                    defaultType: 'radiofield',
                    items: [
                        {
                            boxLabel  : 'Физ. лицо',
                            name      : 'ClientType',
                            inputValue: 'person',
                            itemId    : 'ClientType_Person',
                            disabledCls:'DisabledCls',
                            checked   : true,
                            handler: function() {

                            }
                        }, {
                            boxLabel  : 'Организация',
                            name      : 'ClientType',
                            inputValue: 'company',
                            padding   :  '0 0 0 5',
                            itemId    : 'ClientType_Company',
                            disabledCls:'DisabledCls',
                            handler: function() {

                            }
                        }
                    ]
                },
                /*{
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'Пол',
                    defaultType: 'radiofield',
                    width       : 400,
                    defaults: {
                        flex: 1
                    },
                    layout: 'hbox',
                    items: [
                        {
                            boxLabel  : 'Мужской',
                            name      : 'Gender',
                            inputValue: 'male',
                            itemId    : 'MaleRadio',
                            checked   : true
                        }, {
                            boxLabel  : 'Женский',
                            name      : 'Gender',
                            inputValue: 'female',
                            itemId    : 'FemaleRadio'
                        }
                    ]
                },*/
                {
                    fieldLabel  : 'День рождения',
                    emptyText   : 'выберите дату',
                    name        : 'Birthday',
                    width       : 300,
                    itemId    : 'Birthday',
                    listeners   : {
                        click   : {
                            element: 'el',
                            fn: function() {
                                //alert(1);
                                Ext.create('Ext.window.Window', {
                                    header      : false,
                                    itemId      : 'CalWindow',
                                    //height: 250,
                                    //width: 200,
                                    startDay    : 1,
                                    modal       : true,
                                    layout      : 'fit',
                                    resizable   : false,
                                    items       : {
                                        xtype       : 'datepicker',
                                        //minDate : new Date(),
                                        //height: 150,
                                        showToday   : false,
                                        dateFormat  : "Y",
                                        width       : 200,
                                        flex        : 1,
                                        handler : function(picker, date) {
                                            var dt = new Date(date);
                                            var d  = Ext.Date.format(dt, 'Y-m-d');

                                            Ext.ComponentQuery.query('#Birthday')[0].setValue(d);
                                            Ext.ComponentQuery.query('#CalWindow')[0].close();
                                        }
                                    }
                                }).show();

                                /*
                                 Ext.create('Ext.panel.Panel', {
                                 header  : false,
                                 width   : 200,
                                 floating: true,
                                 bodyPadding: 10,
                                 renderTo: 'cal',
                                 items: [{
                                 xtype   : 'datepicker',
                                 minDate : new Date(),
                                 handler : function(picker, date) {
                                 // do something with the selected date
                                 }
                                 }]
                                 })*/
                            }
                        }
                    }
                },
                {
                    fieldLabel  : 'Email',
                    name        : 'Email',
                    emptyText   : 'pushkin@mail.ru',
                    //afterLabelTextTpl: required,
                    xtype       : 'textfield',
                    allowBlank  : true,
                    vtype       : 'email',
                    width       : 300,
                    getSubmitValue: function() {
                        // "submitEmptyText:false" - не работает (баг Extjs'a), добавляем хак на каждое поле
                        var value = this.getValue();
                        if(Ext.isEmpty(value)) { return null; }
                        return value;
                    }
                    //blankText   : 'Необходимо заполнить поле'
                },
                {
                    fieldLabel  : 'Основной мобильный',
                    emptyText   : '89031234567',
                    name        : 'MobilePhone',
                    itemId      : 'ClientMobilePhone',
                    vtype       : 'DigitsVtype',
                    allowBlank  : false,
                    afterLabelTextTpl: required,
                    blankText   : 'Необходимо заполнить поле',
                    //enableKeyEvents : true,
                    labelWidth  : 200,
                    width       : 400,
                    /*listeners: {
                        keyup: {
                            element: 'el',
                            fn: function( t, e, eOpts ) {

                            }
                        }
                    }*/
                },
                {
                    fieldLabel  : 'Альтернативный мобильный №1',
                    name        : 'MobilePhone1',
                    vtype       : 'DigitsVtype',
                    allowBlank  : true,
                    labelWidth  : 200,
                    width       : 400
                },
                {
                    fieldLabel  : 'Альтернативный мобильный №2',
                    name        : 'MobilePhone2',
                    vtype       : 'DigitsVtype',
                    allowBlank  : true,
                    labelWidth  : 200,
                    width       : 400
                },
                {
                    fieldLabel  : 'Домашний номер',
                    name        : 'HomePhone',
                    vtype       : 'DigitsVtype',
                    allowBlank  : true,
                    labelWidth  : 200,
                    width       : 400
                },
                {
                    fieldLabel  : 'Описание',
                    xtype       : 'textarea',
                    name        : 'Description',
                    itemId      : 'Description',
                    hideLabel   : false,
                    //grow        : false,
                    anchor      : '100%',
                    overflowY   : 'auto',
                    //autoScroll: true,
                    allowBlank  : true,
                    disabledCls : 'DisabledCls',
                    maxHeight   : 100,
                    width       : 300
                }

            ],
            buttons : [
                {
                    text   : 'Сохранить',
                    itemId : 'ClientFormSaveBtn',
                    handler: function() {
                        var SaveButtons = new Array('ClientFormSaveBtn','ClientFormSaveAndCloseBtn'); // Список кнопок для блокировки

                        TriggerSaveButtons('disable', SaveButtons );    // закрываем кнопки от двойного клика
                        Op_ExecAfterWork(                               // ждем завершения ajax запросов в предидущих ф-ях и сохраняем форму
                            function() {
                                SubmitClientForm('ClientWindow', 'ClientTabs', 'ClientForm', 'ClientsGrid', '', false);
                                TriggerSaveButtons('enable', SaveButtons );
                            }
                        );
                    }
                },
                {
                    text    : 'Сохранить и закрыть',
                    itemId  : 'ClientFormSaveAndCloseBtn',
                    handler : function() {
                        var NeedToClose = true; // Закрытие окна
                        var SaveButtons = new Array('ClientFormSaveBtn','ClientFormSaveAndCloseBtn'); // Список кнопок для блокировки

                        TriggerSaveButtons('disable', SaveButtons );    // закрываем кнопки от двойного клика

                        Op_ExecAfterWork(                               // ждем завершения ajax запросов в предидущих ф-ях и сохраняем форму
                            function() {
                                SubmitClientForm('ClientWindow', 'ClientTabs', 'ClientForm', 'ClientsGrid', '', NeedToClose);
                                TriggerSaveButtons('enable', SaveButtons );
                            }
                        );
                    }
                },
                {
                    text    : 'Загрузка данных',
                    hidden  : true,
                    handler : function() {
                        Ext.getCmp('ClientForm').getForm().load({
                            waitMsg:'Идет Загрузка...',
                            url: 'Super.php',
                            method: 'GET',
                            params: {
                                id     : 10,
                                Action : 'OpenObject'},
                            success: function(response, options) {
                                //ClientForm.getForm().setValues(Ext.JSON.decode(response.data));
                                //console.log( Ext.JSON.decode(response.data));
                                //alert(response.data);
                                //alert(response.data.reminder_uid);
                                //Ext.Msg.alert(' ', 'options.result.message' + options.result.message);
                            }
                        });
                    }
                },
                {
                    text: 'Закрыть',
                    handler: function() {
                        //ClientForm.getForm().reset();
                        Ext.getCmp('ClientWindow').close();
                    }
                }]
        } );

        this.callParent(arguments);
    }

});

