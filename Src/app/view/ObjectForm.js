Ext.define('crm.view.ObjectForm', {
    extend      : 'Ext.form.Panel',
    header      : false,
    autoScroll  : true,
    alias       : 'widget.ObjectForm',
    title       : 'Характеристики',
    url         : 'Super.php',
    bodyPadding : 10,
    id          : "ObjectForm",
    defaultType : 'textfield',
    initComponent: function() {
        GlobVars.RegionOrCityUpdated = 0; // маркер: адрес не редактировался #AvitoAltAddr
        Ext.apply(this, {
            id          : "ObjectForm",
            //border: false, // почему не работает??
            items   : [
                // TODO удалить "id" внутри? itemId должно быть достаточно (file:///C:/Temp/ext-4.2.1-gpl/ext-4.2.1.883/docs/index.html#!/api/Ext.AbstractComponent-cfg-itemId)
                // заменить внешние обращения на: p1 = c.getComponent('p1');
                {
                    xtype: 'hiddenfield',
                    name: 'Action',
                    itemId: 'Action',
                    value: 'SaveCityObjectForm'
                },
                {
                    xtype   : 'hiddenfield',
                    name    : 'EditSpecial',
                    itemId  : 'EditSpecial',
                    value   : '0' // 1 - если нужно сохранить только некоторые поля (не всю форму)
                },
                {
                    // Маркер по-умолчанию: форма открыта для объекта в москве   values: Moscow/Oblast
                    xtype: 'hiddenfield',
                    name:  'PositionType',
                    id:    'PositionType',
                    itemId:    'PositionType',
                    value: 'Moscow'
                },
                {
                    // поле по которому обновляется открытый объект
                    // при добавлении сюда добавляется новый id
                    xtype   : 'hiddenfield',
                    name    : 'LoadedObjectId',
                    id      : 'LoadedObjectId', // TODO подсократить idшники
                    itemId  : 'LoadedObjectId'
                },
                //
                {
                    // статичное поле, используемое кнопкой "показать на карте"
                    xtype   : 'hiddenfield',
                    name    : 'Latitude',
                    itemId  : 'Latitude'
                },
                {
                    // маркер наличия ошибок
                    xtype   : 'hiddenfield',
                    name    : 'HasErrors',
                    itemId  : 'HasErrors'
                },
                {
                    // статичное поле, используемое кнопкой "показать адрес"
                    xtype   : 'hiddenfield',
                    name    : 'YandexAddress',
                    itemId  : 'YandexAddress'
                },
                {
                    xtype       : 'fieldcontainer',
                    fieldLabel  : 'Ошибки',
                    hidden      : true,
                    layout      : 'anchor',
                    defaults    : {
                        anchor: '100%'
                    },
                    itemId      : 'ObjectErrorPanel',
                    items: [
                        {
                            xtype       : 'panel',
                            bodyCls     : 'ErrorBackground',
                            itemId      : 'ObjectTodayError',
                            width       : 600,
                            html        : '',
                            style: {
                                borderColor: 'red'
                            }

                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : '',
                    hideLabel  : true,
                    layout     : 'hbox',
                    defaultType: 'textfield',
                    itemId      : 'PriceAgentContainer',
                    items: [
                        {
                            xtype      : 'fieldcontainer',
                            fieldLabel : '',
                            hideLabel  : true,
                            layout     : 'hbox',
                            defaultType: 'textfield',
                            itemId      : 'PriceContainer',
                            items: [

                                {
                                    fieldLabel  : 'Цена',
                                    name        : 'Price',
                                    itemId      : 'Price',
                                    vtype       : 'DigitsVtype',
                                    allowBlank  : false,
                                    width       : 198,
                                    blankText   : 'Необходимо заполнить поле цифрами',
                                    disabledCls : 'DisabledCls'
                                },
                                {
                                    fieldLabel  : ' ',
                                    labelSeparator : ' ',
                                    allowBlank  : false,
                                    name        : 'Currency',
                                    itemId      : 'Currency',
                                    store       : Ext.data.StoreManager.lookup('ObjectForm.CurrencyStore'),
                                    valueField  : 'id',
                                    displayField: 'Text',
                                    xtype       : 'combo',
                                    width       : 50,
                                    editable    : false,
                                    mode        : 'local',
                                    padding     :  '0 0 0 2',
                                    disabledCls : 'DisabledCls',
                                    inputAttrTpl: " data-qclass='ToolTipPinkCls' data-qtitle='Внимание' data-qtip='Avito не поддерживает указание валюты в автоматической выгрузке! Для совместимости со всеми порталами указывайте сумму в рублях, а валюту пишите в описании' ",
                                    disabled    : false, // авито принимает только рубли
                                    listeners: {
                                        afterrender: function(combo) {
                                            Ext.ComponentQuery.query('#Currency')[0].setValue(['70']); // выбор значения по-умолчанию (70=RUB), см. storage
                                        }
                                    }
                                }
                            ]
                        },
                        {
                            xtype      : 'fieldcontainer',
                            fieldLabel : '',
                            hideLabel  : true,
                            layout     : 'hbox',
                            defaultType: 'textfield',
                            itemId      : 'AgentContainer',
                            items: [{
                                    fieldLabel  : 'Агент',
                                    xtype       : 'combo',
                                    id          : 'OwnerUserId',// TODO id - это плохо
                                    itemId      : 'OwnerUserId',
                                    name        : 'OwnerUserId',
                                    triggerAction:  'all',
                                    forceSelection: true,
                                    editable    : false,
                                    allowBlank  : false,
                                    queryParam  : 'GetAgents',
                                    mode        : 'remote',
                                    displayField:'VarName',
                                    valueField  : 'id',
                                    labelWidth  : 50,
                                    width       : 230,
                                    padding     :  '0 0 0 10',
                                    disabledCls : 'DisabledCls',
                                    store: Ext.create('Ext.data.Store', {
                                            fields: [
                                                {name: 'id'},
                                                {name: 'VarName'}
                                            ],
                                            autoLoad: true,
                                            proxy: {
                                                type: 'ajax',
                                                url: 'Super.php?Action=GetObjectFormParams',
                                                reader: {
                                                    type: 'json'
                                                },
                                                extraParams   : {
                                                    OnlyFio  : 1,
                                                    Active   : 1,
                                                    GetAgents: 1
                                                }
                                            }
                                        }
                                    ),
                                    listeners: {
                                        select: {
                                            fn:function(combo, value) {
                                                Ext.ComponentQuery.query('#OwnerPhoneId')[0].reset();
                                            }
                                        }
                                    }
                                },
                                {
                                    hideLabel       : true,
                                    xtype           : 'combo',
                                    itemId          : 'OwnerPhoneId',
                                    name            : 'OwnerPhoneId',
                                    triggerAction   : 'all',
                                    forceSelection  : true,
                                    editable        : false,
                                    allowBlank      : false,
                                    queryParam      : 'GetObjectOwnerPhones',
                                    mode            : 'remote',
                                    displayField    : 'VarName',
                                    valueField      : 'id',
                                    emptyText       : 'тел. номер',
                                    //labelWidth: 98,
                                    width           : 110,
                                    padding         : '0 0 0 5',
                                    disabledCls     : 'DisabledCls',
                                    store           : Ext.data.StoreManager.lookup('ObjectForm.OwnerPhoneStore'),
                                    listeners: {
                                        click: {
                                            element: 'el', //bind to the underlying el property on the panel
                                                fn: function( store, operation, eOpts ) {
                                                var stor = Ext.ComponentQuery.query('#OwnerPhoneId')[0];

                                                // обновляем стор с id объекта
                                                stor.getStore().setProxy({
                                                        type        : 'ajax',
                                                        url         : GetObjectOwnerPhonesUrl,
                                                        reader      : { type: 'json' },
                                                        extraParams : {
                                                            ObjectOwnerId   : Ext.ComponentQuery.query('#OwnerUserId')[0].getValue(),
                                                            GetObjectOwnerPhones : true }
                                                    }
                                                );
                                                stor.getStore().load();
                                            }
                                        }
                                    }
                                },
                                {
                                    text  : 'Доб. корп.',
                                    width       : 65,
                                    padding     : '0 0 0 5',
                                    xtype       : 'text'

                                },
                                {
                                    xtype       : 'checkboxfield',
                                    padding     : '0 0 0 5',
                                    hideLabel   : true,
                                    name        : 'AddCorpPhone',
                                    inputValue  : '1',
                                    inputAttrTpl: " data-qtip='Добавить в выгрузку корпоративный телефонный номер' ",
                                    itemId      : 'AddCorpPhone'
                                }
                            ]
                        }
                    ]
                },

                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'ff',
                    hideLabel : true,
                    layout     : 'hbox',
                    defaultType: 'textfield',
                    items: [
                        {
                            fieldLabel  : 'Тип жилья',
                            name        : 'ObjectAgeType',
                            itemId      : 'ObjectAgeType',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectAgeTypeStore'), //'ObjectAgeTypeStore',//
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',

                            width       : 250,
                            editable    : false,
                            allowBlank  : false,
                            mode        : 'local',

                            disabledCls : 'DisabledCls',
                            listeners: {
                                select: {
                                    fn:function(combo, value) {
                                        var SelectedId = combo.getValue();
                                        ChangeObjectAgeTypeFormTrigger( SelectedId );
                                    }
                                },
                                afterrender: function(combo) {
                                    Ext.ComponentQuery.query('#ObjectAgeType')[0].setValue(['56']); // выбор значения по-умолчанию, см. storage
                                }
                            }
                        },
                        {
                            fieldLabel  : 'Тип объекта',
                            labelWidth  : 90,
                            width       : 230,
                            padding     : '0 0 0 10',
                            xtype       : 'combo',
                            id          : 'ObjectType',
                            itemId      : 'ObjectType',
                            name        : 'ObjectType',
                            triggerAction:  'all',
                            forceSelection: true,
                            editable    : false,
                            allowBlank  : false,
                            queryParam  : 'GetObjectType',
                            mode        : 'remote',
                            valueField  : 'id',
                            displayField: 'Text',
                            disabledCls : 'DisabledCls',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectTypeStore'),
                            listeners: {
                                select: {
                                    fn:function(combo, value) {
                                        var SelectedId = combo.getValue();
                                        console.log( combo.getValue() );
                                        //console.log( value );
                                        ChangeObjectFormTrigger('city', SelectedId);
                                    }
                                },
                                afterrender: function(combo) {
                                    Ext.ComponentQuery.query('#ObjectType')[0].setValue(['1']); // выбор значения по-умолчанию, см. storage
                                }

                            }
                        },
                        {
                            fieldLabel  : 'Комнат всего',
                            name        : 'RoomsCount',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            width       : 170,
                            labelWidth  : 130,
                            padding     : '0 0 0 10',
                            allowBlank  : false,
                            disabledCls : 'DisabledCls'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : '_',
                    hideLabel : true,
                    layout     : 'hbox',
                    defaultType: 'textfield',
                    items: [
                        {
                            fieldLabel  : 'Тип сделки', // для вторички
                            name        : 'DealType',
                            itemId      : 'DealType',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.DealTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 250,
                            editable    : false,
                            //allowBlank  : false,
                            disabledCls : 'DisabledCls',
                            mode        : 'local',
                            inputAttrTpl: " data-qclass='ToolTipYellowCls' data-qtitle='Внимание' data-qtip='Не скрывайте альтернативу под свободной продажей! Рекламные порталы заблокируют ваше объявление.' ",
                            listeners: {
                                afterrender: function(combo) {
                                    Ext.ComponentQuery.query('#DealType')[0].setValue(['58']); // выбор значения по-умолчанию, см. storage
                                }
                            }
                        },
                        {
                            fieldLabel  : 'Тип сделки', // для новостроек
                            hidden      : true,
                            name        : 'NovoDealType',
                            itemId      : 'NovoDealType',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.NovoDealTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 300,
                            editable    : false,
                            allowBlank  : false,
                            disabledCls : 'DisabledCls',
                            mode        : 'local',
                            inputAttrTpl: " data-qclass='ToolTipYellowCls' data-qtitle='Внимание' data-qtip='Не скрывайте альтернативу под свободной продажей! Рекламные порталы заблокируют ваше объявление.' ",
                            listeners: {
                                afterrender: function(combo) {
                                    Ext.ComponentQuery.query('#NovoDealType')[0].setValue(['243']); // выбор значения по-умолчанию, см. storage
                                }
                            }
                        },
                        {
                            text        : 'Утка',
                            width       : 40,
                            padding     : '0 0 0 10',
                            xtype       : 'text'

                        },
                        {
                            xtype       : 'checkboxfield',
                            padding     : '0 0 0 5',
                            hideLabel   : true,
                            name        : 'Utka',
                            inputValue  : '1',
                            inputAttrTpl: " data-qtip='Объект будет выгружаться в отдельной выгрузке' ",
                            itemId      : 'Utka'
                        },
                        {
                            fieldLabel  : 'Долей на продажу',
                            labelWidth  : 115,
                            hidden      : true,
                            disabledCls : 'DisabledCls',
                           // hideMode    : 'visibility',
                            allowBlank  : true,
                            name        : 'PartsSell',
                            itemId      : 'PartsSell',
                            //id          : 'PartsSell',
                            xtype       : 'textfield',
                            vtype       : 'DigitsVtype',
                            width       : 150,
                            padding     : '0 0 0 10'
                        },

                        {
                            fieldLabel  : 'Всего долей',
                            labelWidth  : 90,
                            hidden      : true,
                            allowBlank  : true,
                            name        : 'PartsTotal',
                            itemId      : 'PartsTotal',
                            //id          : 'PartsTotal',
                            vtype       : 'DigitsVtype',
                            disabledCls : 'DisabledCls',
                            width       : 140,
                            padding     : '0 0 0 10'
                        },
                        {
                            fieldLabel  : 'Продаваемых комнат',
                            labelWidth  : 130,
                            hidden      : true,
                            allowBlank  : true,
                            name        : 'RoomsSell',
                            itemId      : 'RoomsSell',
                            id          : 'RoomsSell',
                            vtype       : 'DigitsVtype',
                            disabledCls : 'DisabledCls',
                            width       : 170,
                            padding     : '0 0 0 160'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'Адрес',
                    layout     : 'hbox',
                    defaultType: 'radiofield',
                    items: [
                        {
                            boxLabel  : 'Москва',
                            name      : 'CityType',
                            inputValue: 'Moscow',
                            itemId    : 'CityType_Moscow',
                            disabledCls:'DisabledCls',
                            checked   : true,
                            handler: function() {
                                // открыта вкладка Москва
                                //if (Ext.getCmp('CityType_Moscow').getValue()) {
                                if (Ext.ComponentQuery.query('#CityType_Moscow')[0].getValue()) {
                                    Ext.getCmp('OblastItems1').setVisible(false);  // скрываем поля
                                    //Ext.getCmp('OblastItems2').setVisible(false);
                                    // пропускаем эти поля при сабмите
                                    Ext.getCmp('KladrRegion').setValue('Москва');
                                    //Ext.getCmp('KladrPlaceType').setValue('город');
                                    // TODO убрать getCmp на query
                                    Ext.apply(Ext.getCmp('KladrCity'),      {allowBlank: true}, {}); // во вкладке Москва, заполнение этих полей не нужно
                                    Ext.apply(Ext.getCmp('KladrRaion'),     {allowBlank: true}, {});
                                    Ext.apply(Ext.getCmp('KladrRegion'),    {allowBlank: true}, {});
                                    Ext.apply(Ext.getCmp('KladrPlaceType'), {allowBlank: true}, {});
                                    Ext.apply(Ext.getCmp('MetroStation1Id'),{allowBlank: false}, {});  // метро обязательно в Москве
                                    Ext.apply(Ext.getCmp('MetroWayMinutes'),{allowBlank: false}, {});
                                    Ext.apply(Ext.getCmp('MetroWayType'),   {allowBlank: false}, {});
                                    Ext.getCmp('PositionType').setValue('Moscow'); // маркер определяющий что объект в Москве
                                    return;
                                }
                            }
                        }, {
                            boxLabel  : 'Область',
                            name      : 'CityType',
                            inputValue: 'Oblast',
                            padding   :  '0 0 0 5',
                            itemId    : 'CityType_Oblast',
                            disabledCls:'DisabledCls',
                            handler: function() {
                                // открыта вкладка Область
                                //if (Ext.getCmp('CityType_Oblast').getValue()) {
                                if (Ext.ComponentQuery.query('#CityType_Oblast')[0].getValue()) {
                                    Ext.getCmp('OblastItems1').setVisible(true); // открываем поля
                                    //Ext.getCmp('OblastItems2').setVisible(true);
                                    //Ext.getCmp('KladrCity').setValue('');
                                    //Ext.getCmp('KladrPlaceType').setValue('');
                                    Ext.apply(Ext.getCmp('KladrCity'),      {allowBlank: false}, {}); // во вкладке область помечаем обязательные поля
                                    Ext.apply(Ext.getCmp('KladrRaion'),     {allowBlank: false}, {});
                                    Ext.apply(Ext.getCmp('KladrRegion'),    {allowBlank: false}, {});
                                    Ext.apply(Ext.getCmp('KladrPlaceType'), {allowBlank: false}, {});
                                    Ext.apply(Ext.getCmp('MetroStation1Id'),{allowBlank: true}, {});  // метро становится необязательно в области
                                    Ext.apply(Ext.getCmp('MetroWayMinutes'),{allowBlank: true}, {});
                                    Ext.apply(Ext.getCmp('MetroWayType'),   {allowBlank: true}, {});
                                    Ext.getCmp('PositionType').setValue('Oblast');    // маркер определяющий что объект в области
                                    return;
                                }
                            }
                        },
                        {
                            xtype   : 'text',
                            width   : 280,
                            text    : ' '
                        },
                        Ext.create('Ext.Button', {
                            itemId  : 'GeoWinBtn',
                            iconCls : 'MapCls',
                            disabled: true,
                            text    : 'Посмотреть на карте',
                            padding : '0 0 0 0',
                            disabledCls : 'DisabledCls',
                            handler : function() {
                                // показываем окно с картой
                                // ориентируемся на значение формы Latitude
                                ShowGeoWin(Ext.ComponentQuery.query('#Latitude')[0].value, Ext.ComponentQuery.query('#LoadedObjectId')[0].value);
                            }
                        })
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout     : 'hbox',
                    defaultType: 'textfield',
                    id         : 'OblastItems1',
                    hidden     : true,
                    items: [
                        {
                            fieldLabel  : 'Регион',
                            id          : 'KladrRegion', // TODO убрать id'шники
                            itemId      : 'KladrRegion',
                            name        : 'KladrRegion',
                            value       : 'Московская область',
                            allowBlank  : false,
                            width       : 265,
                            xtype       : 'combobox',
                            disabledCls : 'DisabledCls',
                            anchor      : '100%',
                            hideTrigger : true,
                            displayField: 'Name',
                            valueField  : 'Name',
                            minChars    : 3,
                            queryDelay  : 250,
                            store       : Ext.data.StoreManager.lookup('ObjectForm.KladrStore'),
                            queryParam  : 'KladrRegion',
                            typeAheadDelay: 200,
                            inputAttrTpl: " data-qtip='Область, край, республика. Пример: \"<b>Московская область</b>\", \"<b>Тульская область</b>\", \"<b>Краснодарский край</b>\" ' ",
                            listeners: {
                                change: function(combo, records, eOpts) {
                                    GlobVars.RegionOrCityUpdated = 1; // TODO подумать? #AvitoAltAddr
                                }
                            }
                        },
                        {
                            fieldLabel  : 'Район',
                            allowBlank  : true,
                            itemId      : 'KladrRaion',
                            id          : 'KladrRaion',
                            name        : 'KladrRaion',
                            padding     : '0 0 0 30',
                            disabledCls : 'DisabledCls',
                            width       : 300,
                            xtype       : 'combobox',
                            anchor      : '100%',
                            hideTrigger : true,
                            displayField: 'Name',
                            valueField  : 'Name',
                            minChars    : 3,
                            queryDelay  : 250,
                            store       : Ext.data.StoreManager.lookup('ObjectForm.KladrStore'),
                            queryParam  : 'KladrRaion',
                            typeAheadDelay: 200,
                            inputAttrTpl: " data-qtip='Пример: \"<b>Домодедовский район</b>\", \"<b>Люберецкий район</b>\" ' "
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    defaultType     : 'textfield',
                    hidden          : false,
                    id              : 'OblastItems2',
                    items: [
                        {
                            id          : 'KladrCity',
                            itemId      : 'KladrCity',
                            allowBlank  : true,
                            fieldLabel  : 'Нас. пункт',
                            name        : 'KladrCity',
                            xtype       : 'combobox',
                            anchor      : '100%',
                            disabledCls : 'DisabledCls',
                            width       : 265,
                            hideTrigger : true,
                            valueField  : 'Name',
                            displayField: 'Name',
                            minChars    : 3,
                            queryDelay  : 250,
                            store       : Ext.data.StoreManager.lookup('ObjectForm.KladrStore'),
                            queryParam  : 'KladrCity',
                            typeAheadDelay: 200,
                            value       : 'Москва',
                            inputAttrTpl: " data-qtip='Только название населенного пункта, напр.: \"<b>Химки</b>\", \"<b>Пушкино</b>\", \"<b>Иваново</b>\"' ",
                            listeners: {
                                change: function(combo, records, eOpts) {
                                    GlobVars.RegionOrCityUpdated = 1; // TODO подумать?   #AvitoAltAddr
                                }
                            }
                        },
                        {
                            id          : 'KladrPlaceType',
                            allowBlank  : true,
                            fieldLabel  : 'Тип нас. пункта',
                            name        : 'PlaceType',
                            xtype       : 'combobox',
                            padding     :  '0 0 0 30',
                            disabledCls : 'DisabledCls',
                            width       : 300,
                            anchor      : '100%',
                            hideTrigger : true,
                            displayField: 'Name',
                            minChars    : 1,
                            queryDelay  : 250,
                            store       : Ext.data.StoreManager.lookup('ObjectForm.KladrStore'),
                            queryParam  : 'KladrPlaceType',
                            typeAheadDelay: 200,
                            valueField  : 'Name',
                            editable    : true,
                            value       : 'город',
                            inputAttrTpl: " data-qtip='Заполняйте внимательно, не перепутайте такие значения как, например: <b>Село</b> и <b>Деревня</b>.' "
                        }
                    ]
                },
                {
                    itemId      : 'AltCityFormItem',
                    xtype       : 'fieldcontainer',
                    fieldLabel  : '_',
                    hideLabel   : true,
                    layout      : 'hbox',
                    defaultType : 'textfield',
                    hidden      : true,
                    items: [
                        {
                            itemId      : 'AltCityName',
                            name        : 'AltCityName',    // #AvitoAltAddr
                            allowBlank  : true,
                            fieldLabel  : 'Ближайший  населенный пункт к вашему объекту',
                            xtype       : 'combobox',
                            padding     : '0 0 0 105',
                            labelWidth  : 395,
                            width       : 590,
                            minChars    : 1,
                            queryDelay  : 250,
                            anchor      : '100%',
                            hideTrigger : false,
                            disabledCls : 'DisabledCls',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.AvitoAltCityStore'),
                            queryParam  : 'AltCityName',
                            typeAheadDelay: 200,
                            valueField  : 'Name',
                            displayField: 'Name',
                            editable    : false,
                            inputAttrTpl: " data-qtip='Заполняйте внимательно, не перепутайте такие значения как, например: <b>Село</b> и <b>Деревня</b>.' ",
                            listeners   : {
                                click   : {
                                    element: 'el',
                                    fn: function() {
                                        UpdateAltCityStore(); // обновить поле с новыми параметрами (если нужно) // #AvitoAltAddr
                                    }
                                }
                            }
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel: ' ',
                    labelSeparator : ' ',
                    layout: 'hbox',
                    defaultType: 'textfield',
                    items: [
                        {
                            fieldLabel  : 'Улица',
                            allowBlank  : false,
                            itemId      : 'KladrStreet',
                            name        : 'Street',
                            xtype       : 'combobox',
                            anchor      : '100%',
                            hideTrigger : true,
                            displayField: 'Name',
                            disabledCls : 'DisabledCls',
                            width       : 265,
                            minChars    : 3,
                            queryDelay  : 250,
                            store       : Ext.data.StoreManager.lookup('ObjectForm.KladrStore'),
                            queryParam  : 'KladrStreet',
                            typeAheadDelay: 200,
                            valueField  : 'Name',
                            inputAttrTpl: " data-qtip='Название улицы/переулка/площади и т.д. с указанием типа. Пример: \"<b>Школьная ул</b>\", \"<b>Красная пл</b>\". ' ",
                            listeners   : {
                                click   : {
                                    element: 'el',
                                    fn: function() {
                                        CheckAvitoCompatible(); // #AvitoAltAddr
                                    }
                                }
                            }
                        },
                        {
                            fieldLabel  : '№ дома',
                            allowBlank  : false,
                            name        : 'HouseNumber',
                            width       : 300,
                            padding     : '0 0 0 30',
                            disabledCls : 'DisabledCls',
                            //inputAttrTpl: " data-qtip='Номер дома, корпус, строение. Пример: \"<b>12 к1</b>\", \"<b>5 стр4</b>\", \"<b>21/34</b>\". ' "
                            inputAttrTpl: " data-qclass='ToolTipYellowCls' data-qtitle='Внимание' data-qtip='Заполняйте номер дома точно в таком формате: \"<b>12 к1</b>\", \"<b>5 стр4</b>\", \"<b>21/34</b>\".' ",
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    defaultType     : 'textfield',
                    layout          : 'hbox',
                    items : [
                        {
                            fieldLabel  : 'Этаж',
                            allowBlank  : false,
                            size        : 5,
                            name        : 'Floor',
                            itemId      : 'Floor',
                            vtype       : 'DigitsVtype',
                            width       : 150,
                            disabledCls : 'DisabledCls'
                        }, {
                            fieldLabel  : 'Этажность',
                            allowBlank  : false,
                            name        : 'Floors',
                            itemId      : 'Floors',
                            vtype       : 'DigitsVtype',
                            width       : 150,
                            padding     : '0 0 0 145',
                            disabledCls : 'DisabledCls'
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    itemId          : 'NovoParams',
                    hidden          : true,
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    defaultType     : 'textfield',
                    layout          : 'hbox',
                    items : [
                        {
                            fieldLabel  : 'Название ЖК',
                            allowBlank  : true,
                            name        : 'ObjectBrandName',
                            itemId      : 'ObjectBrandName',
                            //vtype       : 'DigitsVtype',
                            width       : 265,
                            //padding     : '0 0 0 145',
                            disabledCls : 'DisabledCls'
                        },
                        {
                            fieldLabel  : 'Серия дома',
                            name        : 'BuildingSeriesId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.NovoBuildingSeriesStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 300,
                            editable    : false,
                            mode        : 'local',
                            disabledCls : 'DisabledCls',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    hideLabel       : true,
                    id              : 'MetroPanel', // id нужен!
                    itemId          : 'MetroPanel',
                    layout          : 'hbox',
                    items: [
                        {
                            xtype   : 'text',
                            text    : 'Метро:',
                            width : 60
                        },
                        Ext.create('Ext.Button', {
                            itemId  : 'MetroMoreBtn',
                            //iconCls : 'MapCls',
                            disabled: false,
                            height  : 22,
                            text    : 'еще',
                            tooltip : 'Дополнительные станции поддерживаются только базой Winner и Yandex',
                            handler : function() {
                                // если поле доп станций закрыто, показываем дом поля, если
                                //console.log( Ext.ComponentQuery.query('#Metro2Panel')[0].isHidden() );
                                FormMetro_MoreMetroButtons();

                            }
                        }),
                        {
                            fieldLabel  : 'Станция',
                            xtype       : 'combo',
                            itemId      : 'MetroStation1Id',
                            name        : 'MetroStation1Id',
                            triggerAction : 'all',
                            forceSelection: true,
                            editable    : false,
                            allowBlank  : false,
                            queryParam  : 'GetMetroStations',
                            mode        : 'remote',
                            displayField:'VarName',
                            valueField  : 'id',
                            width       : 265,
                            padding     : '0 0 0 10',
                            disabledCls : 'DisabledCls',
                            store: Ext.create('Ext.data.Store', {
                                    fields: [
                                        {name: 'id'},
                                        {name: 'VarName'}
                                    ],
                                    autoLoad: true,
                                    proxy: {
                                        type: 'ajax',
                                        url: 'Super.php?Action=GetObjectFormParams&GetMetroStations=1',
                                        reader: {
                                            type: 'json'
                                        }
                                    }
                                }
                            )
                            /*listeners   : {
                                click   : {
                                    element: 'el',
                                    fn: function() {alert(1);
                                        // если поле доп станций закрыто, показываем дом поля, если
                                    }
                                }
                            }*/
                        },
                        {
                            emptyText   : 'минут',
                            fieldLabel  : 'от метро',
                            name        : 'MetroWayMinutes',
                            itemId      : 'MetroWayMinutes',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            width       : 150,
                            padding     :  '0 0 0 30',
                            allowBlank  : false,
                            inputAttrTpl: " data-qclass='ToolTipYellowCls' data-qtitle='Внимание' data-qtip='Указывайте реальное кол-во минут, не занижайте! Иначе рекламные порталы заблокируют ваше объявление.' ",
                            disabledCls : 'DisabledCls'
                        },
                        {
                            fieldLabel  : ' ',
                            labelSeparator : ' ',
                            name        : 'MetroWayType',
                            //id          : 'MetroWayType',
                            itemId      : 'MetroWayType',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.MetroWayTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 145,
                            padding     : '0 0 0 5',
                            editable    : false,
                            mode        : 'local',
                            allowBlank  : false,
                            disabledCls : 'DisabledCls',
                            listeners: {
                                afterrender: function(combo) {
                                    Ext.ComponentQuery.query('#MetroWayType')[0].setValue(['1']); // выбор значения по-умолчанию, см. storage
                                }
                            }
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    hideLabel       : true,
                    id              : 'Metro2Panel', // id нужен!
                    itemId          : 'Metro2Panel',
                    layout          : 'hbox',
                    hidden          : true,
                    disabled        : true,
                    items: [
                        {
                            xtype   : 'text',
                            text    : ' ',
                            width   : 75
                        },
                        Ext.create('Ext.Button', {
                            disabled: false,
                            height  : 22,
                            text    : 'X',
                            handler : function() {
                                FormMetro_CloseStation('#Metro2Panel');
                                Ext.ComponentQuery.query('#Metro2StationId')[0].setValue(['']);
                                Ext.ComponentQuery.query('#Metro2WayMinutes')[0].setValue('');
                                Ext.ComponentQuery.query('#Metro2WayType')[0].setValue(['']);
                            }
                        }),
                        {
                            fieldLabel  : 'Станция',
                            xtype       : 'combo',
                            itemId      : 'Metro2StationId',
                            name        : 'Metro2StationId',
                            triggerAction : 'all',
                            forceSelection: true,
                            editable    : false,
                            allowBlank  : false,
                            queryParam  : 'GetMetroStations',
                            mode        : 'remote',
                            displayField:'VarName',
                            valueField  : 'id',
                            width       : 265,
                            padding     : '0 0 0 10',
                            disabledCls : 'DisabledCls',
                            store: Ext.create('Ext.data.Store', {
                                    fields: [
                                        {name: 'id'},
                                        {name: 'VarName'}
                                    ],
                                    autoLoad: true,
                                    proxy: {
                                        type: 'ajax',
                                        url: 'Super.php?Action=GetObjectFormParams&GetMetroStations=1',
                                        reader: {
                                            type: 'json'
                                        }
                                    }
                                }
                            )
                        },
                        {
                            emptyText   : 'минут',
                            fieldLabel  : 'от метро',
                            name        : 'Metro2WayMinutes',
                            itemId      : 'Metro2WayMinutes',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            width       : 150,
                            padding     :  '0 0 0 30',
                            allowBlank  : false,
                            disabledCls : 'DisabledCls'
                        }
                        ,
                        {
                            fieldLabel  : ' ',
                            labelSeparator : ' ',
                            name        : 'Metro2WayType',
                            itemId      : 'Metro2WayType',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.MetroWayTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 145,
                            padding     : '0 0 0 5',
                            editable    : false,
                            mode        : 'local',
                            allowBlank  : false,
                            disabledCls : 'DisabledCls',
                            listeners: {
                                afterrender: function(combo) {
                                    Ext.ComponentQuery.query('#Metro2WayType')[0].setValue(['1']); // выбор значения по-умолчанию, см. storage
                                }
                            }
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    hideLabel       : true,
                    id              : 'Metro3Panel',
                    itemId          : 'Metro3Panel',
                    layout          : 'hbox',
                    hidden          : true,
                    disabled        : true,
                    items: [
                        {
                            xtype   : 'text',
                            text    : ' ',
                            width   : 75
                        },
                        Ext.create('Ext.Button', {
                            disabled: false,
                            height  : 22,
                            text    : 'X',
                            handler : function() {
                                FormMetro_CloseStation('#Metro3Panel');
                                Ext.ComponentQuery.query('#Metro3StationId')[0].setValue(['']);
                                Ext.ComponentQuery.query('#Metro3WayMinutes')[0].setValue('');
                                Ext.ComponentQuery.query('#Metro3WayType')[0].setValue(['']);
                            }
                        }),
                        {
                            fieldLabel  : 'Станция',
                            xtype       : 'combo',
                            itemId      : 'Metro3StationId',
                            name        : 'Metro3StationId',
                            triggerAction : 'all',
                            forceSelection: true,
                            editable    : false,
                            allowBlank  : false,
                            queryParam  : 'GetMetroStations',
                            mode        : 'remote',
                            displayField:'VarName',
                            valueField  : 'id',
                            width       : 265,
                            padding     : '0 0 0 10',
                            disabledCls : 'DisabledCls',
                            store: Ext.create('Ext.data.Store', {
                                    fields: [
                                        {name: 'id'},
                                        {name: 'VarName'}
                                    ],
                                    autoLoad: true,
                                    proxy: {
                                        type: 'ajax',
                                        url: 'Super.php?Action=GetObjectFormParams&GetMetroStations=1',
                                        reader: {
                                            type: 'json'
                                        }
                                    }
                                }
                            )
                        },
                        {
                            emptyText   : 'минут',
                            fieldLabel  : 'от метро',
                            name        : 'Metro3WayMinutes',
                            itemId      : 'Metro3WayMinutes',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            width       : 150,
                            padding     :  '0 0 0 30',
                            disabledCls : 'DisabledCls',
                            allowBlank  : false
                        },
                        {
                            fieldLabel  : ' ',
                            labelSeparator : ' ',
                            name        : 'Metro3WayType',
                            itemId      : 'Metro3WayType',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.MetroWayTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 145,
                            padding     : '0 0 0 5',
                            editable    : false,
                            mode        : 'local',
                            allowBlank  : false,
                            disabledCls : 'DisabledCls',
                            listeners: {
                                afterrender: function(combo) {
                                    Ext.ComponentQuery.query('#Metro3WayType')[0].setValue(['1']); // выбор значения по-умолчанию, см. storage
                                }
                            }
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    hideLabel       : true,
                    id              : 'Metro4Panel',
                    itemId          : 'Metro4Panel',
                    layout          : 'hbox',
                    hidden          : true,
                    disabled        : true,
                    items: [
                        {
                            xtype   : 'text',
                            text    : ' ',
                            width   : 75
                        },
                        Ext.create('Ext.Button', {
                            disabled: false,
                            height  : 22,
                            text    : 'X',
                            handler : function() {
                                FormMetro_CloseStation('#Metro4Panel');
                                Ext.ComponentQuery.query('#Metro4StationId')[0].setValue(['']);
                                Ext.ComponentQuery.query('#Metro4WayMinutes')[0].setValue('');
                                Ext.ComponentQuery.query('#Metro4WayType')[0].setValue(['']);
                            }
                        }),
                        {
                            fieldLabel  : 'Станция',
                            xtype       : 'combo',
                            itemId      : 'Metro4StationId',
                            name        : 'Metro4StationId',
                            triggerAction : 'all',
                            forceSelection: true,
                            editable    : false,
                            allowBlank  : false,
                            queryParam  : 'GetMetroStations',
                            mode        : 'remote',
                            displayField:'VarName',
                            valueField  : 'id',
                            width       : 265,
                            padding     : '0 0 0 10',
                            disabledCls : 'DisabledCls',
                            store: Ext.create('Ext.data.Store', {
                                    fields: [
                                        {name: 'id'},
                                        {name: 'VarName'}
                                    ],
                                    autoLoad: true,
                                    proxy: {
                                        type: 'ajax',
                                        url: 'Super.php?Action=GetObjectFormParams&GetMetroStations=1',
                                        reader: {
                                            type: 'json'
                                        }
                                    }
                                }
                            )
                        },
                        {
                            emptyText   : 'минут',
                            fieldLabel  : 'от метро',
                            name        : 'Metro4WayMinutes',
                            itemId      : 'Metro4WayMinutes',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            width       : 150,
                            padding     :  '0 0 0 30',
                            disabledCls : 'DisabledCls',
                            allowBlank  : false
                        },
                        {
                            fieldLabel  : ' ',
                            labelSeparator : ' ',
                            name        : 'Metro4WayType',
                            itemId      : 'Metro4WayType',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.MetroWayTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 145,
                            padding     : '0 0 0 5',
                            editable    : false,
                            mode        : 'local',
                            allowBlank  : false,
                            disabledCls : 'DisabledCls',
                            listeners: {
                                afterrender: function(combo) {
                                    Ext.ComponentQuery.query('#Metro4WayType')[0].setValue(['1']); // выбор значения по-умолчанию, см. storage
                                }
                            }
                        }
                    ]
                },

                {
                    xtype       : 'fieldcontainer',
                    fieldLabel  : 'Площадь',
                    layout      : 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Общая',
                            name        : 'SquareAll',
                            itemId      : 'SquareAll',
                            vtype       : 'DigitsDotVtype',
                            xtype       : 'textfield',
                            allowBlank  : false,
                            disabledCls : 'DisabledCls',
                            width       : 150
                        },
                        {
                            fieldLabel  : 'Жилая',
                            name        : 'SquareLiving',
                            itemId      : 'SquareLiving',
                            vtype       : 'DigitsDotVtype',
                            xtype       : 'textfield',
                            allowBlank  : false,
                            width       : 150,
                            disabledCls : 'DisabledCls',
                            padding     :  '0 0 0 145'
                        },
                        {
                            fieldLabel  : 'Кухня',
                            name        : 'SquareKitchen',
                            itemId      : 'SquareKitchen',
                            vtype       : 'DigitsDotVtype',
                            xtype       : 'textfield',
                            allowBlank  : false,
                            labelWidth  : 90,
                            width       : 145,
                            disabledCls : 'DisabledCls',
                            padding     :  '0 0 0 5'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'Дополнительно',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel: 'Состояние',
                            labelSeparator  : ' ',
                            name        : 'ObjectCondition',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectConditionStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            disabledCls : 'DisabledCls',
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Санузел',
                            name        : 'Toilet',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ToiletStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 300,
                            editable    : false,
                            mode        : 'local',
                            disabledCls : 'DisabledCls',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel: ' ',
                    labelSeparator : ' ',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Тип дома',
                            name        : 'BuildingType',
                            allowBlank  : false,
                            store       : Ext.data.StoreManager.lookup('ObjectForm.BuildingTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            disabledCls : 'DisabledCls',
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Балкон',
                            name        : 'Balcon',
                            id          : 'Balcon',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.BalconStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 300,
                            editable    : false,
                            mode        : 'local',
                            disabledCls : 'DisabledCls',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel: ' ',
                    labelSeparator : ' ',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Вид из окна',
                            name        : 'WindowView',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.WindowViewStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            disabledCls : 'DisabledCls',
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Лифт',
                            name        : 'Lift',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.LiftStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 300,
                            editable    : false,
                            mode        : 'local',
                            disabledCls : 'DisabledCls',
                            padding:  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel: ' ',
                    labelSeparator : ' ',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Телефон',
                            name        : 'Telephone',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.TelephoneStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            disabledCls : 'DisabledCls',
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Ограждения',
                            name        : 'Territory',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.TerritoryStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 300,
                            editable    : false,
                            mode        : 'local',
                            disabledCls : 'DisabledCls',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel: ' ',
                    labelSeparator : ' ',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Мусоропровод',
                            name        : 'Garbage',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.GarbageStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            disabledCls : 'DisabledCls',
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Парковка',
                            name        : 'Parking',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ParkingStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 300,
                            editable    : false,
                            mode        : 'local',
                            disabledCls : 'DisabledCls',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel: ' ',
                    labelSeparator : ' ',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Тип полов',
                            name        : 'Flooring',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.FlooringStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            disabledCls : 'DisabledCls',
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Ипотека',
                            name        : 'Mortgage',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.MortgageStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 300,
                            editable    : false,
                            mode        : 'local',
                            disabledCls : 'DisabledCls',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'Клиент',
                    layout: 'hbox',
                    items: [
                        Ext.create('Ext.Button', {
                            itemId  : 'CreateObjClientBtn',
                            //iconCls : 'MapCls',
                            disabled: false,
                            height  : 22,
                            width   : 265,
                            text    : 'Создать нового клиента',
                            handler : function() {
                                var ClientWindow = Ext.widget('ClientWindow');
                                ClientWindow.setTitle(Words_CreateClientTitle);
                                ClientWindow.show();
                            }
                        }),
                        {
                            hideLabel   : true,
                            emptyText   : 'Выбрать существующего клиента',
                            xtype       : 'combo',
                            itemId      : 'OwnerClientId',
                            name        : 'OwnerClientId',
                            triggerAction : 'all',
                            forceSelection: true,
                            editable    : false,
                            allowBlank  : true, // обязательность регулируется CheckAttachClientToObjectButton()
                            queryParam  : 'GetMyClients',
                            mode        : 'remote',
                            displayField:'VarName',
                            valueField  : 'id',
                            width       : 300,
                            padding     : '0 0 0 30',
                            disabledCls : 'DisabledCls',
                            store: Ext.create('Ext.data.Store', {
                                    fields: [
                                        {name: 'id'},
                                        {name: 'VarName'}
                                    ],
                                    autoLoad: true,
                                    proxy: {
                                        type: 'ajax',
                                        url: 'Super.php?Action=GetMyClients',
                                        reader: {
                                            type: 'json'
                                        }
                                    }
                                }
                            )

                        }

                    ]
                },
                {
                    fieldLabel  : 'Описание',
                    xtype       : 'textarea',
                    name        : 'Description',
                    itemId      : 'Description',
                    hideLabel   : false,
                    anchor      : '100%',
                    overflowY   : 'auto',
                    allowBlank  : false,
                    disabledCls : 'DisabledCls',
                    maxHeight   : 100,
                    minLength   : 100,
                    minLengthText: 'Не ленитесь, составьте интересный текст! Это повысит внимание к вашему объекту! (мин. {0} символов).',
                    inputAttrTpl: " data-qtip='При типе сделки \"Свободная продажа\" запрещено использовать в тексте описания слова \"альтернатива\" или \"нежилой фонд\"! Циан и Winner автоматически заблокируют такой объект.' "
                    //height: 50,
                    //autoScroll: true,
                }
            ],
            buttons : [
                {
                    text   : 'Ошибки исправлены',
                    itemId : 'ObjectFormErrorFixedBtn',
                    hidden : true,
                    iconCls: 'ErrorCls',
                    handler: function() {
                        var ObjectId = Ext.ComponentQuery.query('#LoadedObjectId')[0].value;
                        Ext.Msg.confirm('Подтверждение', 'Нажмите "Yes" если вы исправили ошибку и хотите вернуть рабочий статус объекта',
                            function(btn) {
                                if (btn == 'yes') {

                                    Ext.Ajax.request({
                                        url: 'Super.php',
                                        params: {
                                            ObjectId: ObjectId,
                                            Action: "ObjectErrorFixed"
                                        },
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            if (obj.success == true) {
                                                Ext.getCmp("ObjectsGrid").getStore().load();// обновляем весь грид
                                                Ext.ComponentQuery.query('#ObjectFormErrorFixedBtn')[0].setVisible(false); // скрываем кнопку


                                            } else {
                                                alert('ошибка при ObjectErrorFixed №1');
                                            }
                                        },
                                        failure: function (response, opts) {
                                            alert('ошибка при ObjectErrorFixed №2');
                                        }
                                    });

                                }
                            });
                    }
                },
                {
                    text   : 'Сохранить',
                    itemId : 'ObjectFormSaveBtn',
                    handler: function() {
                        var NeedToClose = false; // Закрытие окна
                        var SaveButtons = new Array('ObjectFormSaveBtn','ObjectFormSaveAndCloseBtn'); // Список кнопок для блокировки
                        if ( CheckObjectFormFields('city') == false ) { // если поля заполнены неправильно, выходим из сохранения
                            return;
                        }
                        var HasErrors = Ext.ComponentQuery.query('#HasErrors')[0].value;
                        if(parseInt(HasErrors) > 0) {
                            // если есть ошибки - напоминаем об исправлении
                            Ext.Msg.confirm('Подтверждение', 'Нажмите "Yes" если вы исправили ошибку и хотите продолжить сохранение',
                                function(btn) {
                                    if (btn == 'yes') {
                                        CitySaveAction(SaveButtons, NeedToClose);
                                    }
                                }
                            );
                        } else {
                            CitySaveAction(SaveButtons, NeedToClose);
                        }
                    }
                },
                {
                    text    : 'Сохранить и закрыть',
                    itemId  : 'ObjectFormSaveAndCloseBtn',
                    handler : function() {
                        var NeedToClose = true; // Закрытие окна
                        var SaveButtons = new Array('ObjectFormSaveBtn','ObjectFormSaveAndCloseBtn'); // Список кнопок для блокировки
                        if ( CheckObjectFormFields('city') == false ) { // если поля заполнены неправильно, выходим из сохранения
                            return;
                        }
                        var HasErrors = Ext.ComponentQuery.query('#HasErrors')[0].value;
                        if(parseInt(HasErrors) > 0) {
                            // если есть ошибки - напоминаем об исправлении
                            Ext.Msg.confirm('Подтверждение', 'Нажмите "Yes" если вы исправили ошибку и хотите продолжить сохранение',
                                function(btn) {
                                    if (btn == 'yes') {
                                        CitySaveAction(SaveButtons, NeedToClose);
                                    }
                                }
                            );
                        } else {
                            CitySaveAction(SaveButtons, NeedToClose);
                        }
                    }
                },
                {
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
                        //ObjectForm.getForm().reset();
                        Ext.getCmp('ObjectWindow').close();
                    }
                }]
        } );
        this.callParent(arguments);
    },
    listeners: {
        render: {
            fn: function() {
                /*Ext.Msg.show({
                    title   :'Приостановлена реклама11',
                    msg     : 'База Winner обнаружила ошибку ... пожалуйста исправьте её и нажмите кнопку "Ошибки исправлены"',
                    buttons : Ext.Msg.OK,
                    icon    : Ext.Msg.ERROR
                });*/
                //alert(123);
                /*var stor = Ext.ComponentQuery.query('#OwnerPhoneId')[0];
                stor.getStore().proxy.extraParams = {  LoadedObjectId   : Ext.getCmp('LoadedObjectId').getValue() };
alert(Ext.getCmp('LoadedObjectId').getValue());
                //stor.getStore().proxy.url = BuildFilterOwnerUserSelectUrlString(MainAjaxDriver, FilterOwnerUserSelect_Action, FilterOwnerUserSelect_ActiveObjects, FilterOwnerUserSelect_GetAgents,FilterOwnerUserSelect_OnlyFio,FilterOwnerUserSelect_WithSumm,FilterOwnerUserSelect_RealtyType);
                stor.getStore().load();
                */
            }
        }
    }

});

