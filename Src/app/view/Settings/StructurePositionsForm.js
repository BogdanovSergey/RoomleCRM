Ext.define('crm.view.Settings.StructurePositionsForm', {
    extend      : 'Ext.form.Panel',
    header      : false,
    autoScroll  : true,
    alias       : 'widget.StructurePositionsForm',
    title       : 'Управление должностями',
    url         : 'Super.php',
    bodyPadding : 10,
    //id          : "ObjectForm",
    layout      : 'vbox',
    defaultType : 'textfield',
    initComponent: function() {
        Ext.apply(this, {
            id      : "StructurePositionsForm",
            items   : [
                {
                    xtype       : 'fieldcontainer',
                    hideLabel   : true,
                    layout      : 'hbox',
                    items: [
                        Ext.create('Ext.Button', {
                            //layout      : 'vbox',
                            iconCls : 'AddCls',
                            text    : 'Создать новую должность',
                            //padding : '10 10 10 0',
                            handler : function() {
                                Ext.Msg.prompt('Название', 'Введите название новой должности:', function(btn, text){
                                    if (btn == 'ok') {
                                        AddNewPositionOrGroupOrStatus('position', text);
                                    }
                                });
                            }
                        }),
                        {
                            xtype   : 'text',
                            width   : 300,
                            text    : ' '
                        },
                        Ext.create('Ext.Button', {
                            iconCls : 'ExpandInOutCls',
                            handler : function() {
                                if(typeof IsExpanded == "undefined") { IsExpanded=false; }
                                if(IsExpanded) {
                                    Ext.getCmp('PositionsTreePanel').collapseAll(function(){});
                                    IsExpanded = false;
                                } else {
                                    Ext.getCmp('PositionsTreePanel').expandAll(function(){});
                                    IsExpanded = true;
                                }

                            }
                        })
                    ]
                },

                {
                    xtype   : 'text',
                    width   : 100,
                    padding : '5 5 5 0'
                    //text    : ' '
                },
                Ext.create('Ext.tree.Panel', {
                    id      : 'PositionsTreePanel',
                    header  : false,
                    width   : 730,//Ext.getCmp('StructureWindow').getWidth(),
                    height  : 300,
                    rootVisible: false,
                    viewConfig: {
                        getRowClass: function(record, index, rowParams) {
                            //if(record.get('id') == 1)    {
                                //record.set('DeleteColumn', 'oioioi');
                            //}
                            //Ext.query('td.x-action-col-cell img', Ext.getCmp('PositionsTreePanel').getNode(index))[0].style.setProperty('background-color','#000');
                            //if(record.get('ItemType') == 'Right')    { return 'ObjectsGridRowColor_LightRed'; }
                            //else if(record.get('Color') == 'LightYellow') { return 'ObjectsGridRowColor_LightYellow'; }
                        }
                    },
                    store: Ext.create('Ext.data.TreeStore', {
                        model: 'crm.model.RightsModel',
                        autoLoad: true,
                        proxy: {
                            type: 'ajax',
                            url: 'Super.php?Action=GetPositionsAndRightsStructure',//&GetAgents=1&Active=1',
                            reader: {
                                type: 'json'
                            }
                        },
                        extraParams : {
                            ExpandItemId : ''
                        }
                    }),
                    //multiSelect: true,
                    columns: [
                    {
                        dataIndex   : 'id',
                        hidden      : true
                    },
                    {
                        dataIndex   : 'ItemType',
                        hidden      : true
                    },
                    {
                        dataIndex   : 'BindToItemId',
                        hidden      : true
                    },
                    {
                        dataIndex   : 'BindToItemName',
                        hidden      : true
                    },
                     {
                        xtype       : 'treecolumn', //this is so we know which column will show the tree
                        text        : 'Название должности и права доступа',
                        width       : 450,
                        menuDisabled: true,
                        dataIndex   : 'ItemName'
                        //locked: true
                    }, {
                        text        : 'Доп. описание',
                        hidden      : true,
                        width       : 150,
                        dataIndex   : 'RightDescr',
                        menuDisabled: true
                    },
                    {
                        text        : 'Изменить',
                        xtype       : 'actioncolumn',
                        //text        : ' ',
                        width       : 60,
                        dataIndex   : 'EditColumn',
                        menuDisabled: true,
                        items   : [{
                            tooltip : 'Изменить название',
                            getClass: function (value, meta, record, rowIndex, colIndex) {
                                if(record.get('ItemType') == 'Right') {
                                    var cls = 'InvisibleItem';  //ничего не показываем
                                } else {
                                    var cls = 'EditCls';      // иконка редактирования
                                }
                                return cls;
                            },
                            handler:function(grid, rowIndex, colIndex, actionItem, event, record, row) {
                                RenameStrucItem('position', record.get('id'), record.get('ItemName'));
                            }
                        }]
                    },

                    {
                        text        : 'Удалить',
                        xtype       : 'actioncolumn',
                        //text        : ' ',
                        width       : 60,
                        dataIndex   : 'DeleteColumn',
                        sortable    : false,
                        items       : [{
                            //tooltip : 'Удалить должность и открепить вложенные права',
                                getClass: function (value, meta, record, rowIndex, colIndex) {
                                    if(record.get('ItemType') == 'Right') {
                                        var cls = 'RemoveRuleCls';  // иконка открепления права доступа
                                    } else {
                                        var cls = 'DeleteCls';      // иконка удаления должности
                                    }
                                    return cls;
                                },
                                handler:function(grid, rowIndex, colIndex, actionItem, event, record, row) {
                                    var rec = grid.getStore().getAt(rowIndex);
                                    if(rec.get('ItemType') === 'Position') {
                                        var descr = rec.get('ItemName');
                                        var WinCaption  = 'Подтвердите удаление должности';
                                        var WinText     = 'Вы действительно хотите удалить должность с названием: ';
                                        var WinText2    = '';
                                    } else if(rec.get('ItemType') === 'Right') {
                                        var descr = rec.get('RightDescr');
                                        var WinCaption  = 'Открепление права доступа';
                                        var WinText     = 'Вы действительно хотите открепить право доступа: ';
                                        var WinText2    = ' от должности: <b>' + rec.get('BindToItemName') +'</b>';
                                    }
                                    DeleteStructureElement(WinCaption, WinText, WinText2, rec.get('ItemType'), rec.get('id'), rec.get('BindToItemId'), descr, 'PositionsTreePanel', 'PositionId', 'position');
                                }
                        }]
                    }
                    ]
                }),
                {
                    xtype   : 'text',
                    width   : 400,
                    padding : '10 10 10 0',
                    text    : 'Прикрепление права доступа к должности:'
                },
                {
                    fieldLabel  : 'Должность',
                    xtype       : 'combo',
                    itemId      : 'PositionId',
                    name        : 'PositionId',
                    triggerAction : 'all',
                    forceSelection: true,
                    editable    : false,
                    //allowBlank  : false,
                    queryParam  : 'GetPositionsArr',
                    mode        : 'remote',
                    displayField:'VarName',
                    valueField  : 'id',
                    width       : 680,
                    labelWidth  : 120,
                    disabledCls : 'DisabledCls',
                    store       : Ext.create('Ext.data.Store', {
                            fields: [
                                {name: 'id'},
                                {name: 'VarName'}
                            ],
                            autoLoad: true,
                            proxy: {
                                type: 'ajax',
                                url: 'Super.php?Action=GetPositionsArr',
                                reader: {
                                    type: 'json'
                                }
                            }
                        }
                    )
                },
                {
                    fieldLabel  : 'Право доступа',
                    xtype       : 'combo',
                    itemId      : 'AccessRuleId',
                    name        : 'AccessRuleId',
                    triggerAction : 'all',
                    forceSelection: true,
                    editable    : false,
                    //allowBlank  : false,
                    queryParam  : 'GetAccessRulesArr',
                    mode        : 'remote',
                    displayField:'VarName',
                    valueField  : 'id',
                    width       : 680,
                    labelWidth  : 120,
                    disabledCls : 'DisabledCls',
                    store       : Ext.data.StoreManager.lookup('StructureRulesStore'),
                    listeners   : {
                        click   : {
                            element: 'el',
                            fn: function() {
                                // меняем параметр для
                                var s = Ext.data.StoreManager.lookup('StructureRulesStore');
                                s.proxy.extraParams = { ChosenItemId  : Ext.ComponentQuery.query('#PositionId')[0].getValue(),
                                                        ChosenItemType : 'position'};
                                s.reload();
                            }
                        }
                    }
                },
                {
                    xtype   : 'text',
                    width   : 100,
                    padding : '5 5 5 0'
                },
                Ext.create('Ext.Button', {
                    iconCls : 'AddRuleCls',
                    text    : 'Прикрепить',
                    handler : function() {
                        if( Ext.ComponentQuery.query('#AccessRuleId')[0].getValue() > 0 &&
                            Ext.ComponentQuery.query('#PositionId')[0].getValue() > 0) {
                                Ext.Ajax.request({
                                    url     : AttachRuleToItemUrl,
                                    params  : {
                                        ItemType   : 'position',
                                        ItemId     : Ext.ComponentQuery.query('#PositionId')[0].getValue(),
                                        RuleId     : Ext.ComponentQuery.query('#AccessRuleId')[0].getValue()
                                    },
                                    success: function(response, opts) {
                                        var PositionsTreePanel = Ext.getCmp('PositionsTreePanel');
                                        var obj = Ext.decode(response.responseText);
                                        Ext.Msg.alert('Успех',  obj.message);

                                        // раскрываем ветку должности после добавления права
                                        PositionsTreePanel.getStore().proxy.extraParams = { ExpandItemId  : Ext.ComponentQuery.query('#PositionId')[0].getValue() };
                                        PositionsTreePanel.getStore().load();


                                        //PositionsTreePanel.getStore().load();
                                        Ext.ComponentQuery.query('#AccessRuleId')[0].reset();
                                    },
                                    failure: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.Msg.alert('Ошибка',  obj.message);
                                    }
                                });
                        }
                    }
                })
            ],
            buttons : [{
                    text: 'Закрыть',
                    handler: function() {
                        Ext.getCmp('StructureWindow').close();
                    }
            }]
        } );
        this.callParent(arguments);
    }/*,
    listeners: {
        close : {
            fn: function() {
               // alert(1222);
            },
            element: 'body'
        }
    }*/

});



