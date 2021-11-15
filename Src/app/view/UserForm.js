Ext.define('crm.view.UserForm' ,{
    extend  : 'Ext.form.Panel',
    alias   : "widget.UserForm",
    header  : false,
    id      : "UserForm",
    title   : 'Характеристики',
    url     : 'Super.php',
    bodyPadding: 10,
    defaultType: 'textfield',
    autoScroll  : true,
    initComponent: function() {
        var required = '<span style="color:red;" data-qtip="Обязательное поле">*</span>';
        Ext.apply(this, {
        items: [
            // TODO удалить "id" внутри? itemId должно быть достаточно (file:///C:/Temp/ext-4.2.1-gpl/ext-4.2.1.883/docs/index.html#!/api/Ext.AbstractComponent-cfg-itemId)
            // заменить внешние обращения на: p1 = c.getComponent('p1');
            {
                xtype   : 'hiddenfield',
                name    : 'Action',
                value   : 'SaveUserForm'
            },
            {
                xtype   : 'hiddenfield',
                name    : 'ResetPassword',
                itemId  : 'ResetPassword',
                value   : '0'
            },
            {
                // поле по которому обновляется открытый пользователь
                // при добавлении сюда добавляется новый id
                xtype: 'hiddenfield',
                name:  'LoadedUserId',
                id:    'LoadedUserId'
            },
            {
                xtype       : 'fieldcontainer',
                fieldLabel  : '',
                hideLabel   : true,
                layout      : 'vbox',
                defaultType : 'textfield',
                defaults: {
                    //flex: 1
                    labelWidth  : 150,
                    width       : 300
                },
                items: [
                    {
                        fieldLabel  : 'Имя Отчество',
                        emptyText   : 'Александр Сергеевич',
                        name        : 'FirstName',
                        allowBlank  : false,
                        blankText   : 'Необходимо заполнить поле',
                        afterLabelTextTpl: required,
                        width       : 400
                    },
                    {
                        fieldLabel  : 'Фамилия',
                        emptyText   : 'Пушкин',
                        name        : 'LastName',
                        allowBlank  : false,
                        blankText   : 'Необходимо заполнить поле',
                        afterLabelTextTpl: required,
                        width       : 400
                    },
                    {
                        fieldLabel  : 'Основной мобильный',
                        emptyText   : '89031234567',
                        name        : 'MobilePhone',
                        itemId      : 'UserFormMobilePhone',
                        vtype       : 'DigitsVtype',
                        allowBlank  : false,
                        afterLabelTextTpl: required,
                        blankText   : 'Необходимо заполнить поле',
                        enableKeyEvents : true,
                        width       : 400,
                        listeners: {
                            keyup: {
                                element: 'el',
                                fn: function( t, e, eOpts ) {
                                    var NeedCopy = Ext.ComponentQuery.query('#CopyPhoneChkbx')[0].getValue();
                                    if(NeedCopy) {                          // копируем телефон в поле логин, если надо
                                        var loginField = Ext.ComponentQuery.query('#UserFormLogin')[0];
                                        var phone = Ext.ComponentQuery.query('#UserFormMobilePhone')[0].getValue();
                                        loginField.setValue(phone);
                                    }
                                }
                            }
                        }
                    },
                    {
                        fieldLabel  : 'Должность',
                        xtype       : 'combo',
                        itemId      : 'Pos0Id',
                        name        : 'Pos0Id',
                        triggerAction:'all',
                        forceSelection: true,
                        editable    : false,
                        afterLabelTextTpl: required,
                        allowBlank  : false,
                        queryParam  : 'GetPositionsList',
                        mode        : 'remote',
                        displayField: 'PositionName',
                        valueField  : 'id',
                        //labelWidth  : 98,
                        width       : 400,
                        //padding     : '0 0 0 32',
                        store: Ext.create('Ext.data.Store', {
                                fields: [
                                    {name: 'id'},
                                    {name: 'PositionName'}
                                ],
                                autoLoad: true,
                                proxy: {
                                    type: 'ajax',
                                    url : 'Super.php?Action=GetPositionsList&Active=1',
                                    reader: {
                                        type: 'json'
                                    }
                                }
                            }
                        ),
                        listeners: {
                            select: {
                                fn:function(combo, value) {
                                    var SelectedId = combo.getValue();

                                    LoadUserAccessRightsStructure(Ext.ComponentQuery.query('#LoadedUserId')[0].getValue(),
                                        SelectedId,
                                        Ext.ComponentQuery.query('#Group0Id')[0].getValue());

                                    SetRightsStoragesExtraParams(Ext.ComponentQuery.query('#LoadedUserId')[0].getValue(),
                                        SelectedId,
                                        Ext.ComponentQuery.query('#Group0Id')[0].getValue());
                                }
                            }
                        }
                    },
                    {
                        fieldLabel  : 'Отдел',
                        xtype       : 'combo',
                        itemId      : 'Group0Id',
                        name        : 'Group0Id',
                        triggerAction    :'all',
                        forceSelection   : true,
                        editable         : false,
                        afterLabelTextTpl: required,
                        allowBlank  : false,
                        queryParam  : 'GetGroupsList',
                        mode        : 'remote',
                        displayField: 'GroupName',
                        valueField  : 'id',
                        //labelWidth  : 98,
                        width       : 400,
                        //padding     : '0 0 0 32',
                        store: Ext.create('Ext.data.Store', {
                                fields: [
                                    {name: 'id'},
                                    {name: 'GroupName'}
                                ],
                                autoLoad: true,
                                proxy: {
                                    type: 'ajax',
                                    url : 'Super.php?Action=GetGroupsList&Active=1',
                                    reader: {
                                        type: 'json'
                                    }
                                }
                            }
                        ),
                        listeners: {
                            select: {
                                fn:function(combo, value) {
                                    var SelectedId = combo.getValue();
                                    // загрузить в области список прав
                                    // пересоздать грид с новыми id
                                    LoadUserAccessRightsStructure(Ext.ComponentQuery.query('#LoadedUserId')[0].getValue(),
                                        Ext.ComponentQuery.query('#Pos0Id')[0].getValue(), SelectedId);

                                    SetRightsStoragesExtraParams(Ext.ComponentQuery.query('#LoadedUserId')[0].getValue(),
                                        Ext.ComponentQuery.query('#Pos0Id')[0].getValue(),
                                        SelectedId
                                    );
                                }
                            }
                        }
                    },
                    {
                        fieldLabel  : 'Статус',
                        xtype       : 'combo',
                        itemId      : 'Status0Id',
                        name        : 'Status0Id',
                        triggerAction:'all',
                        forceSelection: true,
                        editable    : false,
                        //afterLabelTextTpl: required,
                        allowBlank  : true,
                        queryParam  : 'GetStatusesList',
                        mode        : 'remote',
                        displayField: 'StatusName',
                        valueField  : 'id',
                        width       : 400,
                        store: Ext.create('Ext.data.Store', {
                                fields: [
                                    {name: 'id'},
                                    {name: 'StatusName'}
                                ],
                                autoLoad: true,
                                proxy: {
                                    type: 'ajax',
                                    url : 'Super.php?Action=GetStatusesList&Active=1',
                                    reader: {
                                        type: 'json'
                                    }
                                }
                            }
                        )
                    },
                    {
                        fieldLabel  : 'Email',
                        name        : 'Email',
                        emptyText   : 'pushkin@mail.ru',
                        afterLabelTextTpl: required,
                        allowBlank  : false,
                        vtype       : 'email',
                        width       : 400
                        //blankText   : 'Необходимо заполнить поле'
                    },
                    {
                        fieldLabel  : 'День рождения',
                        emptyText   : 'выберите дату',
                        name        : 'Birthday',
                        width       : 250,
                        itemId      : 'Birthday',
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
                        xtype       : 'fieldcontainer',
                        fieldLabel  : '',
                        hideLabel   : true,
                        layout      : 'hbox',
                        width       : 450,
                        defaults: {
                            labelWidth  : 150
                        },
                        items: [
                            {
                                fieldLabel  : 'Логин',
                                name        : 'Login',
                                itemId      : 'UserFormLogin',
                                afterLabelTextTpl: required,
                                allowBlank  : false,
                                xtype       : 'textfield',
                                vtype       : 'alphanum',
                                width       : 250
                                //blankText   : 'Необходимо заполнить поле'
                            },
                            {
                                xtype     : 'checkboxfield',
                                boxLabel  : 'копировать мобильный',
                                itemId    : 'CopyPhoneChkbx',
                                //width     : 100,
                                padding : '0 0 0 10',
                                handler: function() {
                                    var ChkbxVal   = Ext.ComponentQuery.query('#CopyPhoneChkbx')[0].getValue();
                                    var loginField = Ext.ComponentQuery.query('#UserFormLogin')[0];
                                    loginField.setReadOnly( ChkbxVal );          // открываем/закрываем ручной ввод
                                    if(ChkbxVal == true) {                       // галка установлена - копируем телефон сюда в поле
                                        var phone = Ext.ComponentQuery.query('#UserFormMobilePhone')[0].getValue();
                                        loginField.setValue(phone);
                                        loginField.addCls('x-item-disabled');    // меняем цвет
                                    } else {
                                        loginField.removeCls('x-item-disabled'); // возвращаем нормальный цвет
                                    }
                                }
                            }
                        ]
                    },
                    {
                        xtype       : 'fieldcontainer',
                        fieldLabel  : '',
                        hideLabel   : true,
                        layout      : 'hbox',
                        width       : 450,
                        defaults: {
                            labelWidth  : 150
                        },
                        items: [
                            {
                                fieldLabel       : 'Пароль',
                                name             : 'Password1',
                                itemId           : 'ClosedPassword1',
                                allowBlank       : true,
                                afterLabelTextTpl: required,
                                xtype            : 'textfield',
                                inputType        : 'password',
                                width            : 250,
                                hidden           : false,
                                disabled         : false
                            },
                            {
                                fieldLabel       : 'Пароль',        // спрятанное поле для раскрытия "звездочек"
                                name             : 'Password1',
                                itemId           : 'OpenedPassword1',
                                allowBlank       : true,
                                afterLabelTextTpl: required,
                                xtype            : 'textfield',
                                inputType        : 'text',
                                width            : 250,
                                hidden           : true,
                                disabled         : true
                            },
                            Ext.create('Ext.Button', {
                                itemId  : 'GeneratePasswordBtn',
                                disabled: false,
                                height  : 22,
                                text    : 'сгенерировать',
                                //padding : '0 0 0 10',
                                handler : function() {
                                    var NewPass         = GeneratePassword(),
                                        ClosedPassField = Ext.ComponentQuery.query('#ClosedPassword1')[0],
                                        OpenedPassField = Ext.ComponentQuery.query('#OpenedPassword1')[0];
                                    ClosedPassField.setValue( NewPass ); // устанавливаем ноывй пароль в поля
                                    OpenedPassField.setValue( NewPass );
                                }
                            }),
                            {
                                xtype     : 'checkboxfield',
                                boxLabel  : 'открыть',
                                itemId    : 'PassChkbx',
                                padding   : '0 0 0 10',
                                handler: function() {
                                    var ChkbxVal = Ext.ComponentQuery.query('#PassChkbx')[0].getValue();
                                    var ClosedPassField = Ext.ComponentQuery.query('#ClosedPassword1')[0];
                                    var OpenedPassField = Ext.ComponentQuery.query('#OpenedPassword1')[0];
                                    if(ChkbxVal) {
                                        // надо открыть OpenedPassField и закрыть ClosedPassField
                                        OpenedPassField.setValue( ClosedPassField.getValue() );
                                        OpenedPassField.setVisible(true);
                                        OpenedPassField.setDisabled(false);
                                        ClosedPassField.setVisible(false);
                                        ClosedPassField.setDisabled(true);
                                    } else {
                                        ClosedPassField.setValue( OpenedPassField.getValue() );
                                        OpenedPassField.setVisible(false);
                                        OpenedPassField.setDisabled(true);
                                        ClosedPassField.setVisible(true);
                                        ClosedPassField.setDisabled(false);
                                    }
                                }
                            },
                            {
                                fieldLabel       : 'Пароль',        // спрятанное поле для отражения "старого пароля"
                                //name             : 'Password1',
                                itemId           : 'OldPassword',
                                allowBlank       : true,
                                value            : 'старый пароль',
                                afterLabelTextTpl: required,
                                xtype            : 'textfield',
                                inputType        : 'text',
                                width            : 250,
                                hidden           : true,
                                disabled         : true
                            },
                            Ext.create('Ext.Button', {              // для открытия полей с полем для нового пароля
                                itemId  : 'CreatePasswordBtn',
                                disabled: false,
                                hidden  : true,
                                height  : 22,
                                text    : 'создать новый',
                                //padding : '0 0 0 10',
                                handler : function() {
                                    var ClosedPassField = Ext.ComponentQuery.query('#ClosedPassword1')[0],
                                        OpenedPassField = Ext.ComponentQuery.query('#OpenedPassword1')[0],
                                        GenBtn          = Ext.ComponentQuery.query('#GeneratePasswordBtn')[0],
                                        ChkBtn          = Ext.ComponentQuery.query('#PassChkbx')[0],
                                        OldPassField    = Ext.ComponentQuery.query('#OldPassword')[0],
                                        CreatePassBtn   = Ext.ComponentQuery.query('#CreatePasswordBtn')[0];
                                    OldPassField.setVisible(false);             // СКРЫВАЕМ поле "старого" пароля
                                    CreatePassBtn.setVisible(false);            // и кнопку
                                    ClosedPassField.setVisible(true);   // ОТКРЫТЬ поле с вводом пароля
                                    GenBtn.setVisible(true);            // кнопку "сгенерировать"
                                    ChkBtn.setVisible(true);            // галку открыть пароль

                                    var ResetMarker = Ext.ComponentQuery.query('#ResetPassword')[0];
                                    ResetMarker.setValue(1);                // ставим пометку о сбросе пароля
                                }
                            })
                        ]
                    },



                    {
                        hidden      : true,
                        fieldLabel  : 'Подтверждение пароля',
                        name        : 'password2',
                        allowBlank  : true,
                        inputType   : 'password'
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
                        width       : 400
                    },
                    {
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
                    },
                    {
                        xtype   : 'text',
                        width   : 450,
                        text    : 'При добавлении/изменении анкеты сотрудник получит оповещение на email'
                    },
                    {
                        fieldLabel  : 'Оповещение',
                        hidden : true,
                        name        : 'UserInvitation',
                        itemId      : 'UserInvitation',
                        id          : 'UserInvitation',
                        store       : Ext.data.StoreManager.lookup('UserNotificationsStore'),
                        valueField  : 'id',
                        displayField: 'Text',
                        xtype       : 'combo',
                        width       : 400,
                        editable    : false,
                        disabled    : false,
                        mode        : 'local',
                        allowBlank  : true,
                        listeners: {
                            afterrender: function(combo) {
                                //Ext.getCmp('UserInvitation').setValue(1);
                                Ext.ComponentQuery.query('#UserInvitation')[0].setValue(1); // выбор значения по-умолчанию, см. storage
                                //alert(22);
                            }
                        }
                    }
                ]
            }
        ],
        buttons: [
            {
                text    : 'Сохранить',
                handler : function() {
                    SubmitUserForm(false);
                }
            },
            {
                text: 'Сохранить и закрыть',
                handler: function() {
                    var NeedToClose = true;
                    SubmitUserForm(NeedToClose);
                }
            },
            {
                text: 'Загрузка данных',
                hidden: true,
                handler: function() {
                    var UserForm = Ext.getCmp('UserForm');
                    UserForm.getForm().load({
                        waitMsg:'Идет Загрузка...',
                        url: 'Super.php',
                        method: 'GET',
                        params:{
                            id:10,
                            Action:'OpenUser'},
                        success: function(response, options) {
                            //ObjectForm.getForm().setValues(Ext.JSON.decode(response.data));
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
                    var UserWindow = Ext.getCmp('UserWindow');
                    UserWindow.close();
                }
            }]
        });

        this.callParent(arguments);
    }
});
