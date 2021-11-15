Ext.define('crm.view.Settings.StructureGroupsForm', {
    extend      : 'Ext.form.Panel',
    header      : false,
    autoScroll  : true,
    alias       : 'widget.StructureGroupsForm',
    title       : 'Управление отделами',
    url         : 'Super.php',
    bodyPadding : 10,
    //id          : "ObjectForm",
    defaultType : 'textfield',
    initComponent: function() {
        Ext.apply(this, {
            id          : "StructureGroupsForm",
            items   : [
                {
                    xtype       : 'fieldcontainer',
                    hideLabel   : true,
                    layout      : 'hbox',
                    items: [
                        Ext.create('Ext.Button', {
                            iconCls : 'AddCls',
                            text    : 'Создать новый отдел',
                            handler : function() {
                                Ext.Msg.prompt('Название', 'Введите название нового отдела:', function(btn, text){
                                    if (btn == 'ok') {
                                        AddNewPositionOrGroupOrStatus('group', text);
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
                                if(typeof IsGExpanded == "undefined") { IsGExpanded=false; }
                                if(IsGExpanded) {
                                    Ext.getCmp('GroupsTreePanel').collapseAll(function(){});
                                    IsGExpanded = false;
                                } else {
                                    Ext.getCmp('GroupsTreePanel').expandAll(function(){});
                                    IsGExpanded = true;
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
                    id      : 'GroupsTreePanel',
                    header  : false,
                    width   : 730,
                    height  : 300,
                    rootVisible: false,
                    store: Ext.create('Ext.data.TreeStore', {
                        model   : 'crm.model.RightsModel',
                        autoLoad: true,
                        proxy: {
                            type    : 'ajax',
                            url     : 'Super.php?Action=GetGroupsAndRightsStructure',
                            reader  : {
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
                            text        : 'Название отдела и права доступа',
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
                                    RenameStrucItem('group', record.get('id'), record.get('ItemName'));
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
                                    if(rec.get('ItemType') === 'Group') {
                                        var descr = rec.get('ItemName');
                                        var WinCaption  = 'Подтвердите удаление отдела';
                                        var WinText     = 'Вы действительно хотите удалить отдел с названием: ';
                                        var WinText2    = '';
                                    } else if(rec.get('ItemType') === 'Right') {
                                        var descr = rec.get('RightDescr');
                                        var WinCaption  = 'Открепление права доступа';
                                        var WinText     = 'Вы действительно хотите открепить право доступа: ';
                                        var WinText2    = ' от отдела: <b>' + rec.get('BindToItemName') +'</b>';
                                    }
                                    DeleteStructureElement(WinCaption, WinText, WinText2, rec.get('ItemType'), rec.get('id'), rec.get('BindToItemId'), descr, 'GroupsTreePanel', 'GroupId', 'group');
                                }
                            }]
                        }
                    ]
                }),
                {
                    xtype   : 'text',
                    width   : 400,
                    padding : '10 10 10 0',
                    text    : 'Прикрепление права доступа к отделу:'
                },
                {
                    fieldLabel  : 'Отдел',
                    xtype       : 'combo',
                    itemId      : 'GroupId',
                    name        : 'GroupId',
                    triggerAction : 'all',
                    forceSelection: true,
                    editable    : false,
                    //allowBlank  : false,
                    queryParam  : 'GetGroupsArr',
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
                                url: 'Super.php?Action=GetGroupsArr',
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
                                s.proxy.extraParams = { ChosenItemId   : Ext.ComponentQuery.query('#GroupId')[0].getValue(),
                                                        ChosenItemType : 'group'};
                                s.reload();
                            }
                        }
                    }
                },
                /*{
                    xtype   : 'text',
                    width   : 100,
                    padding : '5 5 5 0'
                },*/
                Ext.create('Ext.Button', {
                    iconCls : 'AddRuleCls',
                    text    : 'Прикрепить',
                    handler : function() {
                        if( Ext.ComponentQuery.query('StructureGroupsForm > #AccessRuleId')[0].getValue() > 0 &&
                            Ext.ComponentQuery.query('#GroupId')[0].getValue() > 0) {
                            Ext.Ajax.request({
                                url     : AttachRuleToItemUrl,
                                params  : {
                                    ItemType   : 'group',
                                    ItemId     : Ext.ComponentQuery.query('#GroupId')[0].getValue(),
                                    RuleId     : Ext.ComponentQuery.query('StructureGroupsForm > #AccessRuleId')[0].getValue()
                                },
                                success: function(response, opts) {
                                    var GroupsTreePanel = Ext.getCmp('GroupsTreePanel');
                                    var obj = Ext.decode(response.responseText);
                                    Ext.Msg.alert('Успех',  obj.message);

                                    // раскрываем ветку должности после добавления права
                                    GroupsTreePanel.getStore().proxy.extraParams = { ExpandItemId  : Ext.ComponentQuery.query('#GroupId')[0].getValue() };
                                    GroupsTreePanel.getStore().load();


                                    //GroupsTreePanel.getStore().load();
                                    Ext.ComponentQuery.query('StructureGroupsForm > #AccessRuleId')[0].reset();
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
    }

});



