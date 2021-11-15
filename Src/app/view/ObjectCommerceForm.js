Ext.define('crm.view.ObjectCommerceForm',{
    extend      : 'Ext.form.Panel',
    header      : false,
    autoScroll  : true,
    //id          : "ObjectCommerceForm",
    alias       : 'widget.ObjectCommerceForm',
    title       : 'Характеристики',
    url         : 'Super.php',
    bodyPadding : 10,
    defaultType : 'textfield',
    //autoRender  : true,
    initComponent: function() {
        GlobVars.RegionOrCityUpdated = 0; // маркер: адрес не редактировался #AvitoAltAddr
        Ext.apply(this, {
            id      : "ObjectCommerceForm",
            items   : [
                {
                    xtype   : 'hiddenfield',
                    name    : 'Action',
                    itemId  : 'Action',
                    value   : 'SaveCommerceObjectForm'
                },
                {
                    // Маркер по-умолчанию: форма открыта для объекта в москве   values: Moscow/Oblast
                    xtype   : 'hiddenfield',
                    name    : 'PositionType',
                    itemId  : 'PositionType',
                    value   : 'Moscow'
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
                    xtype : 'hiddenfield',
                    name  :  'LoadedObjectId',
                    itemId: 'LoadedObjectId'
                },
                {
                    // статичное поле, используемое кнопкой "показать на карте"
                    xtype   : 'hiddenfield',
                    name    : 'Latitude',
                    itemId  : 'Latitude'
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : ' ',
                    labelSeparator : ' ',
                    hideLabel: true,
                    layout     : 'hbox',
                    defaultType: 'textfield',
                    itemId     : 'PriceAgentContainer',
                    items: [
                        {
                            xtype      : 'fieldcontainer',
                            fieldLabel : ' ',
                            labelSeparator : ' ',
                            hideLabel  : true,
                            layout     : 'hbox',
                            defaultType: 'textfield',
                            itemId     : 'PriceContainer',
                            items: [
                                {
                                    emptyText   : 'Цена',
                                    fieldLabel  : 'Основное',
                                    name        : 'Price',
                                    id          : 'Price',
                                    vtype       : 'DigitsVtype',
                                    allowBlank  : false,
                                    width       : 208,
                                    blankText   : 'Необходимо заполнить поле цифрами'
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
                                    padding     :  '0 0 0 3',
                                    disabled    : false, // авито принимает только рубли
                                    inputAttrTpl: " data-qclass='ToolTipPinkCls' data-qtitle='Внимание' data-qtip='Avito не поддерживает указание валюты в автоматической выгрузке! Для совместимости со всеми порталами указывайте сумму в рублях, а валюту пишите в описании' ",
                                    listeners: {
                                        afterrender: function(combo) {
                                            Ext.ComponentQuery.query('#Currency')[0].setValue(['70']); // выбор значения по-умолчанию (70=RUB), см. storage
                                        }
                                    }
                                },
                                {
                                    emptyText   : 'Период оплаты',
                                    name        : 'CommercePricePeriodId',
                                    itemId      : 'CommercePricePeriodId',
                                    store       : Ext.data.StoreManager.lookup('ObjectForm.CommercePricePeriodStore'),
                                    valueField  : 'id',
                                    displayField: 'Text',
                                    xtype       : 'combo',
                                    width       : 120,
                                    editable    : false,
                                    allowBlank  : false,
                                    padding     :  '0 0 0 3',
                                    mode        : 'local',
                                    listeners: {
                                        afterrender: function(combo) {
                                            //Ext.ComponentQuery.query('#PriceTypeId')[0].setValue(['122']); // выбор значения по-умолчанию, см. storage
                                        }
                                    }
                                },
                                {
                            //fieldLabel  : 'Тип цены',
                            emptyText   : 'Тип цены',
                            name        : 'CommercePriceTypeId',
                            itemId      : 'CommercePriceTypeId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommercePriceTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 110,
                            editable    : false,
                            allowBlank  : false,
                            mode        : 'local',
                            padding     : '0 0 0 3',
                            listeners: {
                                afterrender: function(combo) {
                                    //Ext.ComponentQuery.query('#PriceTypeId')[0].setValue(['122']); // выбор значения по-умолчанию, см. storage
                                }
                            }
                        }
                            ]
                        },
                        {
                            xtype      : 'fieldcontainer',
                            fieldLabel : ' ',
                            labelSeparator : ' ',
                            hideLabel: true,
                            layout     : 'hbox',
                            defaultType: 'textfield',
                            itemId     : 'AgentContainer',
                            items: [{
                                fieldLabel  : 'Агент',
                                xtype       : 'combo',
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
                                width       : 245,
                                labelWidth  : 40,
                                padding     :  '0 0 0 7',
                                store: Ext.create('Ext.data.Store', {
                                        fields: [
                                            {name: 'id'},
                                            {name: 'VarName'}
                                        ],
                                        autoLoad: true,
                                        proxy: {
                                            type: 'ajax',
                                            url: 'Super.php?Action=GetObjectFormParams&GetAgents=1&Active=1&OnlyFio=1',
                                            reader: {
                                                type: 'json'
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
                            }

                            ]
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    items           : [
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
                            //labelWidth: 98,
                            width           : 100,
                            emptyText       : 'тел. номер',
                            padding         : '0 0 0 445',
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
                            text        : 'Доб. корп.',
                            width       : 79,
                            padding     : '0 0 0 10',
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
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel: ' ',
                    labelSeparator : ' ',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Тип сделки',
                            name        : 'DealType',
                            itemId      : 'DealType',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCommerceDealTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 280,
                            labelWidth  : 110,
                            editable    : false,
                            mode        : 'local',
                            allowBlank  : false
                        },
                        {
                            fieldLabel  : 'Тип помещений',
                            name        : 'CommerceRoomTypeId',
                            itemId      : 'CommerceRoomTypeId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceRoomTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 355,
                            labelWidth  : 150,
                            editable    : false,
                            padding     : '0 0 0 10',
                            mode        : 'local',
                            allowBlank  : false
                        }
                    ]
                },

                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    items           : [
                        {
                            fieldLabel  : 'Название объекта',
                            itemId      : 'ObjectBrandName',
                            name        : 'ObjectBrandName',
                            xtype       : 'textfield',
                            allowBlank  : true,
                            width       : 280,
                            labelWidth  : 110
                        },
                        {
                            fieldLabel  : 'Назначение помещения',
                            xtype       : 'combo',
                            itemId      : 'CommerceObjectTypeId',
                            name        : 'CommerceObjectTypeId',
                            triggerAction:  'all',
                            forceSelection: true,
                            editable    : false,
                            allowBlank  : false,
                            //queryParam  : 'GetAgents',
                            mode        : 'remote',
                            displayField:'VarName',
                            valueField  : 'id',
                            width       : 355,
                            labelWidth  : 150,
                            padding     :  '0 0 0 10',
                            store: Ext.create('Ext.data.Store', {
                                    fields: [
                                        {name: 'id'},
                                        {name: 'VarName'}
                                    ],
                                    autoLoad: true,
                                    proxy   : {
                                        type    : 'ajax',
                                        url     : 'Super.php?Action=GetObjectFormParams&GetCommerceObjectTypeList=1',
                                        reader  : {
                                            type: 'json'
                                        }
                                    }
                                }
                            )
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
                            id        : 'CityType_Moscow',
                            checked   : true,
                            handler: function() {
                                // открыта вкладка Москва
                                if (Ext.getCmp('CityType_Moscow').getValue()) {
                                    //Ext.getCmp('OblastItems1').setVisible(false);  // скрываем поля
                                    Ext.ComponentQuery.query('#KladrRegion')[0].setValue('Москва');
                                    return;
                                }
                            }
                        }, {
                            boxLabel  : 'Область',
                            name      : 'CityType',
                            inputValue: 'Oblast',
                            id        : 'CityType_Oblast',
                            padding     :  '0 0 0 5',
                            handler: function() {
                                // открыта вкладка Область
                                if (Ext.getCmp('CityType_Oblast').getValue()) {
                                    //Ext.getCmp('OblastItems1').setVisible(true); // открываем поля
                                    Ext.ComponentQuery.query('#KladrRegion')[0].setValue('Московская область');
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
                    hidden     : false,
                    items: [
                        {
                            fieldLabel  : 'Регион',
                            itemId      : 'KladrRegion',
                            name        : 'KladrRegion',
                            value       : 'Московская область',
                            allowBlank  : false,
                            width       : 265,
                            xtype       : 'combobox',
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
                            allowBlank  : false,
                            fieldLabel  : 'Нас. пункт',
                            name        : 'KladrCity',
                            itemId      : 'KladrCity',
                            xtype       : 'combobox',
                            anchor      : '100%',
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
                            /*tpl: Ext.create('Ext.XTemplate',
                             '<tpl for=".">',
                             '<div class="x-boundlist-item">{Name} --- {Socr}</div>',
                             '</tpl>'
                             )*/
                        },
                        {
                            id          : 'KladrPlaceType',
                            allowBlank  : true,
                            fieldLabel  : 'Тип нас. пункта',
                            name        : 'PlaceType',
                            xtype       : 'combobox',
                            padding     :  '0 0 0 30',
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
                            allowBlank  : true,
                            name        : 'HouseNumber',
                            width       : 150,
                            padding     :  '0 0 0 30'
                        }

                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel: ' ',
                    labelSeparator : ' ',
                    defaultType: 'textfield',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Этаж',
                            allowBlank  : true,
                            size        : 5,
                            name        : 'Floor',
                            itemId      : 'Floor',
                            vtype       : 'DigitsVtype',
                            width       : 150
                        }, {
                            fieldLabel  : 'Этажность',
                            allowBlank  : true,
                            name        : 'Floors',
                            itemId      : 'Floors',
                            vtype       : 'DigitsVtype',
                            //id:     'lalala',
                            width       : 150,
                            padding     : '0 0 0 145'
                        }
                    ]
                },

                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Станция метро',
                            xtype       : 'combo',
                            id          : 'MetroStation1Id',
                            name        : 'MetroStation1Id',
                            triggerAction:  'all',
                            forceSelection: true,
                            editable    : false,
                            allowBlank  : true,
                            queryParam  : 'GetMetroStations',
                            mode        : 'remote',
                            displayField:'VarName',
                            valueField  : 'id',
                            width       : 265,
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
                            name        : 'MetroWayMinutes',
                            id          : 'MetroWayMinutes',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            width       : 150,
                            padding     :  '0 0 0 30',
                            allowBlank  : true
                        },
                        {
                            fieldLabel  : ' ',
                            labelSeparator : ' ',
                            name        : 'MetroWayType',
                            id          : 'MetroWayType',
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
                            listeners: {
                                afterrender: function(combo) {
                                    Ext.ComponentQuery.query('#MetroWayType')[0].setValue(['1']); // выбор значения по-умолчанию, см. storage
                                }
                            }
                        }
                    ]
                },


                {
                    xtype       : 'fieldcontainer',
                    fieldLabel  : 'Площадь (S)',
                    layout      : 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Комнат всего',
                            name        : 'RoomsCount',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            width       : 150,
                            //padding     : '0 0 0 40',
                            allowBlank  : true
                        },
                        {
                            fieldLabel  : 'Общая',
                            emptyText   : 'м2',
                            name        : 'SquareAll',
                            itemId      : 'SquareAll',
                            vtype       : 'DigitsDotVtype',
                            xtype       : 'textfield',
                            allowBlank  : false,
                            width       : 101,
                            labelWidth  : 45,
                            padding     : '0 0 0 13'
                        },
                        {
                            fieldLabel  : 'S по комнатам',
                            emptyText   : '20+15+40+50',
                            value       : '',
                            name        : 'CommerceRoomsSquares',
                            itemId      : 'CommerceRoomsSquares',
                            xtype       : 'textfield',
                            //allowBlank  : false,
                            width       : 328,
                            labelWidth  : 110,
                            padding     :  '0 0 0 30'
                        }
                    ]
                },


                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    items: [

                        {
                            fieldLabel  : 'Min площадь (если сдается по частям)',
                            name        : 'CommerceSquareMin',
                            itemId      : 'CommerceSquareMin',
                            vtype       : 'DigitsDotVtype',
                            xtype       : 'textfield',
                            labelWidth  : 120,
                            width       : 190
                        },
                        {
                            fieldLabel  : 'Зданий в комплексе',
                            name        : 'CommerceBuildingsCount',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            labelWidth  : 130,
                            width       : 180,
                            padding     :  '0 0 0 10'
                        },
                        {
                            fieldLabel  : 'Высота потолков',
                            name        : 'CommerceCeilingHeight',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CeilingHeightStore'),
                            valueField  : 'val',
                            displayField: 'val',
                            xtype       : 'combo',
                            width       : 195,
                            labelWidth  : 110,
                            editable    : false,
                            mode        : 'local',
                            padding     : '0 0 0 10'
                        }
                    ]
                },

                {
                    xtype       : 'fieldcontainer',
                    fieldLabel  : 'Характеристики',
                    layout      : 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Тип здания',
                            name        : 'CommerceBuildingTypeId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceBuildingTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Год введения',
                            name        : 'CommerceBuildingYear',
                            xtype       : 'textfield',
                            vtype       : 'DigitsVtype',
                            width       : 140,
                            labelWidth  : 95,
                            padding     :  '0 0 0 30'
                        },
                        {
                            fieldLabel  : 'Класс зданий',
                            name        : 'CommerceBuildingClass',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceBuildingClassStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            labelWidth  : 85,
                            width       : 150,
                            editable    : false,
                            mode        : 'local',
                            padding     : '0 0 0 10'
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                        items : [
                        {
                            fieldLabel  : 'Коммунальные платежи',
                            name        : 'CommerceCommunPayId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceCommunPayStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Эксплуатационные расходы',
                            name        : 'CommerceExplutPayId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceExplutPayStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 303,
                            labelWidth  : 110,
                            editable    : false,
                            mode        : 'local',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Планировка',
                            name        : 'RoomMapId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceRoomMapStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Наличие мебели',
                            name        : 'CommerceFurnitureId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceFurnitureStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 303,
                            labelWidth  : 110,
                            editable    : false,
                            mode        : 'local',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },


                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    items : [
                        {
                            fieldLabel  : 'Состояние',
                            name        : 'CommerceConditionId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceConditionStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Статус здания',
                            name        : 'CommerceBuildingStatusId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceBuildingStatusStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 303,
                            labelWidth  : 110,
                            editable    : false,
                            mode        : 'local',
                            padding     : '0 0 0 30'
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
                            fieldLabel  : 'Пожаротушение',
                            name        : 'CommerceFireId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceFireStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Кондиционирование',
                            name        : 'CommerceVentilationId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceVentilationStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 305,
                            labelWidth  : 120,
                            editable    : false,
                            mode        : 'local',
                            padding     : '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    items : [
                        {
                            fieldLabel  : 'Отопление',
                            name        : 'CommerceHeatingId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceHeatingStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Выделенная мощность на м2 (кВт)',
                            name        : 'CommercePower',
                            itemId      : 'CommercePower',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            allowBlank  : true,
                            labelWidth  : 260,
                            width       : 320,
                            padding     :  '0 0 0 30'
                        }
                    ]
                },


                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    items : [
                        {
                            fieldLabel  : 'Вход в здание',
                            name        : 'CommerceEnterTypeId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceEnterTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Допустимая нагрузка на перекрытия (кг/м2)',
                            name        : 'CommerceFloorLoad',
                            itemId      : 'CommerceFloorLoad',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            allowBlank  : true,
                            labelWidth  : 260,
                            width       : 320,
                            padding     : '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    items : [
                        {
                            fieldLabel  : 'Парковка',
                            name        : 'CommerceParkingId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceParkingStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            //labelWidth  : 220,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Парк. мест включено в стоимость',
                            name        : 'CommerceParkingPlaces',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            labelWidth  : 260,
                            width       : 320,
                            padding     : '0 0 0 30',
                            allowBlank  : true
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
                            fieldLabel  : 'Кол-во тел. линий',
                            name        : 'CommercePhoneLinesCount',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            width       : 265,
                            labelWidth  : 150
                            //padding     : '0 0 0 40',
                            //allowBlank  : false
                        },
                        {
                            fieldLabel  : 'Дополнительные тел. линии',
                            name        : 'CommercePhoneLinesAddId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommercePhoneLinesAddStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            labelWidth  : 260,
                            width       : 340,
                            editable    : false,
                            mode        : 'local',
                            padding     : '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    items           : [
                        {
                            fieldLabel  : 'Марка лифта',
                            name        : 'LiftBrand',
                            itemId      : 'LiftBrand',
                            xtype       : 'textfield',
                            allowBlank  : true,
                            width       : 265,
                            labelWidth  : 150
                        },
                        {
                            fieldLabel  : 'Кол-во лифтов',
                            name        : 'CommerceLifts',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CommerceLiftsStore'),
                            valueField  : 'val',
                            displayField: 'val',
                            xtype       : 'combo',
                            width       : 150,
                            editable    : false,
                            mode        : 'local',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },

                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Телеком провайдер',
                            name        : 'TelecomProvider',
                            itemId      : 'TelecomProvider',
                            xtype       : 'textfield',
                            allowBlank  : true,
                            width       : 265,
                            labelWidth  : 150

                        },
                        {

                            xtype       : 'checkboxfield',
                            boxLabel    : 'Объект подключен к интернет',
                            name        : 'OptionInternet',
                            itemId      : 'OptionInternet',
                            inputValue  : '1',
                            //afterLabelTextTpl: '123'
                            padding     : '0 0 0 30'
                        }
                    ]
                },

                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    defaultType     : 'checkboxfield',
                    items           : [
                        {
                            boxLabel  : 'Наличие санузла в блоке',
                            name      : 'OptionToilet',
                            itemId    : 'OptionToilet',
                            inputValue: '1'
                        },{
                            boxLabel  : 'кафе',
                            name      : 'OptionCafe',
                            itemId    : 'OptionCafe',
                            inputValue: '1'
                        },
                        {
                            boxLabel  : 'банкомат',
                            name      : 'OptionBankomat',
                            itemId    : 'OptionBankomat',
                            inputValue: '1'
                        },
                        {
                            boxLabel  : 'фитнесс',
                            name      : 'OptionFitness',
                            itemId    : 'OptionFitness',
                            inputValue: '1'
                        },{
                            boxLabel  : 'магазин',
                            name      : 'OptionShop',
                            itemId    : 'OptionShop',
                            inputValue: '1'
                        }
                    ]
                },


                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : 'Комиссия',
                    //labelSeparator  : ' ',
                    layout          : 'hbox',
                    items: [
                        {
                            fieldLabel  : '% агенту',
                            name        : 'CommerceAgentPay',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            width       : 180,
                            labelWidth  : 120,
                            //padding     : '0 0 0 40',
                            allowBlank  : true
                        },
                        {
                            fieldLabel  : '% клиенту',
                            name        : 'CommerceClientPay',
                            itemId      : 'CommerceClientPay',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            allowBlank  : true,
                            width       : 180,
                            labelWidth  : 120,
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
                    minLengthText: 'Не ленитесь, составьте интересный текст! Это повысит внимание к вашему объекту! (мин. {0} символов).'
                    //height    : 50,
                    //autoScroll: true,
                }
            ],
            buttons : [
                {
                    text    : 'Сохранить',
                    itemId  : 'ObjectFormSaveBtn',
                    handler : function() {
                        var SaveButtons = new Array('ObjectFormSaveBtn','ObjectFormSaveAndCloseBtn'); // Список кнопок для блокировки
                        if ( CheckObjectFormFields('commerce') == false ) { // если поля заполнены неправильно, выходим из сохранения
                            return;
                        }
                        TriggerSaveButtons('disable', SaveButtons );    // закрываем кнопки от двойного клика
                        CheckAvitoCompatible();                         // проверка и изменение формы для авито #AvitoAltAddr
                        Op_ExecAfterWork(                               // ждем завершения ajax запросов в предидущих ф-ях и сохраняем форму
                            function() {
                                SubmitObjectForm('ObjectCommerceWindow', 'ObjectCommerceTabs', 'ObjectCommerceForm', 'ObjectsCommerceGrid', 'ObjectCommerceAdditionsForm', false);
                                TriggerSaveButtons('enable', SaveButtons );
                            }
                        );
                    }
                },
                {
                    text    : 'Сохранить и закрыть',
                    itemId  : 'ObjectFormSaveAndCloseBtn',
                    handler : function() {
                        var NeedToClose = true; // Закрытие окна
                        var SaveButtons = new Array('ObjectFormSaveBtn','ObjectFormSaveAndCloseBtn'); // Список кнопок для блокировки
                        if ( CheckObjectFormFields('commerce') == false ) { // если поля заполнены неправильно, выходим из сохранения
                            return;
                        }
                        TriggerSaveButtons('disable', SaveButtons );    // закрываем кнопки от двойного клика
                        CheckAvitoCompatible();                         // проверка и изменение формы для авито #AvitoAltAddr
                        Op_ExecAfterWork(                               // ждем завершения ajax запросов в предидущих ф-ях и сохраняем форму
                            function() {
                                SubmitObjectForm('ObjectCommerceWindow', 'ObjectCommerceTabs', 'ObjectCommerceForm', 'ObjectsCommerceGrid', 'ObjectCommerceAdditionsForm', NeedToClose);
                                TriggerSaveButtons('enable', SaveButtons );
                            }
                        );
                    }
                },
                {
                    text    : 'Загрузка данных',
                    hidden  : true,
                    handler : function() {
                        //var b = Ext.getCmp('OwnerUserId');
                        //b.setValue(2);
                        Ext.getCmp('ObjectCommerceForm').getForm().load({
                            waitMsg:'Идет Загрузка...',
                            url: 'Super.php',
                            method: 'GET',
                            params:{
                                id      : 0,
                                Action  : 'OpenObject'},
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
                        Ext.getCmp('ObjectCommerceWindow').close();
                    }
                }]
        } );
        this.callParent(arguments);
    }

});

