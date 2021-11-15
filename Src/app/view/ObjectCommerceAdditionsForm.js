Ext.define('crm.view.ObjectCommerceAdditionsForm', {
    extend      : 'Ext.form.Panel',
    id          : "ObjectCommerceAdditionsForm",
    alias       : 'widget.ObjectCommerceAdditionsForm',
    url         : 'Super.php',
    disabled    : true,
    collapsible : true,
    frame       : true,
    title       : 'Дополнительно',
    initComponent: function() {
        Ext.apply(this, {
            id          : "ObjectCommerceAdditionsForm",
            items: [
                {
                    xtype   : 'hiddenfield',
                    name    : 'Action',
                    value   : 'SaveAdditionsObjectForm'
                },
                {
                    // поле по которому обновляется открытый объект
                    // при добавлении сюда добавляется новый id
                    xtype   : 'hiddenfield',
                    name    : 'LoadedObjectId',
                    itemId  : 'LoadedObjectId'
                },
                {
                    xtype       : 'fieldcontainer',
                    fieldLabel  : 'Для сайта',
                    //labelSeparator : ' ',
                    defaultType: 'textfield',
                    layout      : 'hbox',
                    items       : [
                        {
                            fieldLabel  : 'Title',
                            allowBlank  : true,
                            name        : 'SiteTitle',
                            itemId      : 'SiteTitle',
                            width       : 600
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    defaultType     : 'textfield',
                    layout          : 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Meta-keywords',
                            allowBlank  : true,
                            name        : 'SiteKeywords',
                            itemId      : 'SiteKeywords',
                            width       : 600
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    defaultType     : 'textfield',
                    layout          : 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Meta-description',
                            allowBlank  : true,
                            name        : 'SiteDescription',
                            itemId      : 'SiteDescription',
                            xtype       : 'textarea',
                            width       : 600,
                            height      : 50,
                            maxHeight   : 50
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : ' ',
                    labelSeparator : ' ',
                    defaultType: 'textfield',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Ссылка на видео',
                            allowBlank  : true,
                            name        : 'SiteVideo',
                            itemId      : 'SiteVideo',
                            width       : 600
                        }
                    ]
                },
                {
                    xtype       : 'fieldcontainer',
                    fieldLabel  : 'Изменение типа объекта',
                    labelWidth  : 205,
                    defaultType: 'textfield',
                    layout      : 'hbox',
                    items       : [
                        {
                            fieldLabel     : ' ',
                            labelSeparator : ' ',
                            xtype       : 'combo',
                            itemId      : 'RealtyType',
                            name        : 'RealtyType',
                            triggerAction : 'all',
                            forceSelection: true,
                            editable    : false,
                            allowBlank  : false,
                            queryParam  : 'GetRealtyTypes',
                            mode        : 'remote',
                            displayField: 'VarData',
                            valueField  : 'VarName',
                            width       : 200,
                            store: Ext.create('Ext.data.Store', {
                                    fields: [
                                        {name: 'id'},
                                        {name: 'VarName'},
                                        {name: 'VarData'}
                                    ],
                                    autoLoad: true,
                                    proxy: {
                                        type: 'ajax',
                                        url: 'Super.php?Action=GetObjectFormParams&GetRealtyTypes=1',
                                        reader: {
                                            type: 'json'
                                        }
                                    }
                                }
                            ),
                            listeners:{
                                'select': function() {
                                    Ext.Msg.show({
                                        title   :'Важно',
                                        msg     : Words_ObjectRealtyTypeChanged,
                                        buttons : Ext.Msg.OK,
                                        icon    : Ext.Msg.INFO
                                    });

                                }
                            }
                        }
                    ]
                }
            ],
            buttons : [
                {
                    text    : 'Сохранить',
                    itemId  : 'ObjectFormSaveBtn',
                    handler: function() {
                        SubmitAdditionalForm('ObjectCommerceAdditionsForm', 'ObjectCommerceTabs', 'ObjectCommerceWindow', false);
                    }
                },
                {
                    text    : 'Сохранить и закрыть',
                    itemId  : 'ObjectFormSaveAndCloseBtn',
                    handler: function() {
                        var NeedToClose = true;
                        SubmitAdditionalForm('ObjectCommerceAdditionsForm', 'ObjectCommerceTabs', 'ObjectCommerceWindow', NeedToClose);
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
                                id:10,
                                Action:'OpenObject'},
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

        });

        this.callParent(arguments);
    }
});

