
function WhileOpeningTheUserForm(UserId, PositionId, GroupId) {
    console.log( arguments.callee.name + '(UserId:'+UserId+', PositionId:'+PositionId+', GroupId:'+GroupId+')' );
    // функции при открытии и изменении пользователя

    // строим ячейки
    // собираем компоненты окна
    Ext.getCmp('UserWindow').add( {
        header  : false,
        border  : false,
        itemId  : 'LeftVerticalPanel'
    } );
    Ext.ComponentQuery.query('#LeftVerticalPanel')[0].add( Ext.create('crm.view.UserForm') );
    Ext.getCmp('UserWindow').add( Ext.create('Ext.panel.Panel', {
        header  : false,
        itemId  : 'RightVeriticalPanelsGroup',
        //id  : 'RightVeriticalPanelsGroup',
        //baseCls : 'x-plain',
        border      : false,
        //autoScroll  : true,
        flex    : 1,
        items   : [{
                header      : false,
                itemId      : 'Oqwerqwer',
                border      : false,
                bodyPadding : 5,
                html        : '<img src="images/siluet_city2.jpg" height="80px" border="1px">'
            },
            {
                xtype       : 'panel',
                layout      : 'fit',
                header      : false,
                border      : false,
                bodyPadding : 0,
                height      : 150,
                width       : 400,
                autoScroll  : false,
                itemId      : 'RightsFrame'
            },
            {
                xtype       : 'form',
                header      : false,
                border      : false,
                layout      : 'fit',
                bodyPadding : 0,
                height      : 180,
                width       : 400,
                autoScroll  : false,
                itemId      : 'NewRightsFrame'
            }
        ]
    }) );

    if(UserId > 0) {

        // таблицв с имеющимися правами
        LoadUserAccessRightsStructure(UserId, PositionId, GroupId);

        // запрос на новые права
        LoadAccessRulesForAddition(UserId, PositionId, GroupId);
    }
}

function SetRightsStoragesExtraParams(UserId, PositionId, GroupId) {
    // Загрузить и раскрыть все права поьзователя или сделать предварительный просмотр прав на должность/отдел
    console.log( arguments.callee.name + '(UserId:'+UserId+', PositionId:'+PositionId+', GroupId:'+GroupId+')' );
    var s = Ext.data.StoreManager.lookup('UserFormRightsStore');
    s.proxy.extraParams = { UserId      : UserId,
                            PositionId  : PositionId,
                            GroupId     : GroupId};
    s.reload();
    //
    var s = Ext.data.StoreManager.lookup('UsersRightsForAdditionStore');
    s.proxy.extraParams = { UserId      : UserId,
                            PositionId  : PositionId,
                            GroupId     : GroupId};
    s.reload();
}

function LoadUserAccessRightsStructure(UserId, PositionId, GroupId) {
    console.log( arguments.callee.name + '(UserId:'+UserId+', PositionId:'+PositionId+', GroupId:'+GroupId+')' );
    Ext.destroy( Ext.getCmp('AccessRulesGrid') ); // Удалить прежний грид с устаревшими id в кнопках
    //var result = Ext.decode(response.responseText);
    var RuleObj     = new Array();
    var RightsFrame = Ext.ComponentQuery.query('#RightsFrame')[0];
    Ext.destroy( Ext.getCmp('AccessRulesGrid') );
    var AccessRulesGrid = Ext.create('Ext.grid.Panel', {
        store       : Ext.data.StoreManager.lookup('UserFormRightsStore'),
        hideHeaders : true,
        height      : 200,
        features    : [{ ftype: 'grouping' }],
        //title   : 'Права для добавления',
        id : 'AccessRulesGrid',
        columns: [
            {
                hidden  : true,
                dataIndex: 'id'
            },
            {
                hidden  : true,
                dataIndex: 'TargetType'
            },
            {
                menuDisabled: true,
                text        : 'Активные права',
                width       : 325,
                dataIndex   : 'Description'
            },
            {
                menuDisabled: true,
                xtype       : 'actioncolumn',
                text        : 'Открепить',
                width       : 40,
                sortable    : false,
                dataIndex   : 'RemoveColumn',
                items       : [{
                    iconCls : 'RemoveRuleCls',
                    tooltip : 'Открепить право ',//+UserId+'-'+PositionId+'-'+GroupId,
                    getClass: function (value, meta, record, rowIndex, colIndex) {
                        if(record.get('TargetType') == 'user') {
                            var cls = 'RemoveRuleCls';  // личные права можно убрать
                        } else {
                            var cls = 'InvisibleItem';      //ничего не показываем
                        }
                        return cls;
                    },
                    handler:function (grid, rowIndex, colIndex) {
                        var rec = grid.getStore().getAt(rowIndex);
                        Ext.Ajax.request({
                            url     : DeleteAccessRuleIdForUserIdUrl,
                            params  : {
                                UserId  : UserId,
                                RuleId  : rec.get('id')
                            },
                            success: function(response, opts) {
                                SetRightsStoragesExtraParams(UserId, PositionId, GroupId);

                            },
                            failure: function(response, opts) {
                                alert('Невозможно добавить право "'+RuleObj.Description+'".\n' + Words_CallProgrammerMsg);
                            }
                        });
                    }
                }]
            }
        ]
    });
    RightsFrame.add( AccessRulesGrid );

    var s = Ext.data.StoreManager.lookup('UserFormRightsStore');
    s.proxy.extraParams = { UserId      : UserId,
                            PositionId  : PositionId,
                            GroupId     : GroupId};
    s.reload();
    s.sort('Description', 'ASC'); // отсортировать!

}


function LoadAccessRulesForAddition(UserId, PositionId, GroupId) {
    console.log( arguments.callee.name + '('+UserId+')' );
    // отрисовываем список прав для добавления
    Ext.Ajax.request({
        url     : GetAccessRulesForAdditionUrl,
        params  : {
            UserId  : UserId
        },
        success: function(response, opts) {
            var result = Ext.decode(response.responseText);

            var RuleObj          = new Array();

            // расставляем все красиво
            var RightsFrame = Ext.ComponentQuery.query('#NewRightsFrame')[0];
            var NRGrid = Ext.create('Ext.grid.Panel', {
                store: Ext.data.StoreManager.lookup('UsersRightsForAdditionStore'),
                header      : true,
                hideHeaders : true,
                menuDisabled: true,
                height  : 300,
                title   : 'Неактивные права',
                id : 'NRGrid',
                columns: [
                    {
                        hidden   : true,
                        dataIndex: 'id'
                    },
                    {
                        text        : 'Права для добавления',
                        width       : 325,
                        dataIndex   : 'Description'
                    },
                    {
                        xtype       : 'actioncolumn',
                        text        : 'Добавить',
                        width       : 40,
                        sortable    : false,
                        dataIndex   : 'add',
                        items       : [{
                            iconCls : 'AddRuleCls',
                            handler:function (grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                //alert( UserId + '--' + rec.get('id') );

                                Ext.Ajax.request({
                                    url     : AddAccessRuleIdForUserIdUrl,
                                    params  : {
                                        UserId  : UserId,
                                        RuleId  : rec.get('id')
                                    },
                                    success: function(response, opts) {
                                        // ОБНОВЛЯЕМ ГРИД Прав для добавления
                                        var s = Ext.data.StoreManager.lookup('UsersRightsForAdditionStore');
                                        //s.proxy.extraParams = { UserId : UserId };
                                        s.reload();
                                        // Обновляем грид-структуру
                                        var s = Ext.data.StoreManager.lookup('UserFormRightsStore');
                                        //s.proxy.extraParams = { UserId : UserId };
                                        s.reload();
                                    },
                                    failure: function(response, opts) {
                                        alert('Невозможно добавить право "'+RuleObj.Description+'".\n' + Words_CallProgrammerMsg);
                                    }
                                });

                            }
                        }]
                    }
                ]
            });
            RightsFrame.add( NRGrid );
            //RightsFrame.add( Ext.create('crm.view.NRGrid') );

            var s = Ext.data.StoreManager.lookup('UsersRightsForAdditionStore');
            s.proxy.extraParams = { UserId : UserId };
            s.reload();
        },
        failure: function(response, opts) {
            var msg = 'ОШИБКА: невозможно получить список прав для добавления';
            alert(msg); // TODO протоколировать!!!
        }
    });
}


function SubmitUserForm(NeedToClose) {
    // выясняем открытое поле с паролем и запрещаем ввод пустого или маленького пароля
    var ClosedPassField = Ext.ComponentQuery.query('#ClosedPassword1')[0],
        OpenedPassField = Ext.ComponentQuery.query('#OpenedPassword1')[0],
        OldPassword     = Ext.ComponentQuery.query('#OldPassword')[0],
        CurrentPasswd   = '';
    if(ClosedPassField.isVisible() == true) {
        CurrentPasswd = ClosedPassField.getValue();
    } else if(OpenedPassField.isVisible() == true) {
        CurrentPasswd = OpenedPassField.getValue();
    } else if(OldPassword.isVisible() == true) {
        CurrentPasswd = 'заглушка для старого пароля чтобы пройти проверку на длину';
    } else {
        alert('Невозможно определить пароль.\n' + Words_CallProgrammerMsg);
    }

    if(CurrentPasswd.length <= 3) {
        Ext.Msg.confirm('Слишком маленький пароль', 'Вы действительно хотите установить такой короткий пароль? Это может послужить причиной взлома данного профиля.',
            function(btn) {
                if (btn == 'yes') {
                    SubmitFinish(NeedToClose);
                }
            }
        );
    } else {
        SubmitFinish(NeedToClose)
    }
}

function SubmitFinish(NeedToClose) {
    var UserForm = Ext.getCmp('UserForm');
    UserForm.getForm().submit({
        waitMsg:'Идет отправка...',
        success: function(form, action) {
            var UserWindow = Ext.getCmp('UserWindow');
            if(action.result.LoadedUserId > 0) {
                // обновляем заголовок окна
                UserWindow.setTitle(Words_EditUserTitle + ' №' + action.result.LoadedUserId);

                // добавляем/обновляем в форму id нового/существующего объекта
                UserForm.down('#LoadedUserId').setValue(action.result.LoadedUserId);

                // отрисовываем список прав для нового пользователя
                LoadAccessRulesForAddition( action.result.LoadedUserId,
                    Ext.ComponentQuery.query('#Pos0Id')[0].getValue(),
                    Ext.ComponentQuery.query('#Group0Id')[0].getValue());
            }
            Ext.Msg.alert('Успех',  action.result.message);
            // TODO сделать обновление грида опциональным (для слабого канала - не требуется)
            console.log('обновляем UsersGrid...');
            Ext.getCmp('UsersGrid').getStore().load();// обновляем грид

            if(NeedToClose) {
                UserWindow.close();
            }
        },
        failure: function(form, action) {
            switch (action.failureType) {
                case Ext.form.action.Action.CLIENT_INVALID:
                    Ext.Msg.alert('Ошибка', 'Пожалуйста, правильно заполните все обязательные поля!');
                    break;
                case Ext.form.action.Action.CONNECT_FAILURE:
                    Ext.Msg.alert('Ошибка', 'CONNECT_FAILURE: проблема со связью');
                    break;
                case Ext.form.action.Action.SERVER_INVALID:
                    Ext.Msg.alert('Ошибка', '' + action.result.message);
            }
        }
    });
}


function ArchivateUserById(rowIndex, UserId, Lastname) {
    Ext.Msg.confirm('Архивация пользователя', 'Отправить в архив пользователя <b>' + Lastname + '</b> (№' + UserId + ') ?' + '<br><br><img src="icons/star.png"> Объекты пользователя не удаляются и продолжат выходить в рекламу!',
        function(btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    type    : 'ajax',
                    url     : ArchivateUserUrl,
                    params  : {
                        UserId: UserId
                    },
                    success: function(response, opts) {
                        var obj = Ext.decode(response.responseText);
                        if(obj.success == true) {
                            // TODO если выбрана настройка "быстрый инет" - обновлять сторэдж, иначе не обновлять
                            var stor = Ext.data.StoreManager.lookup('UsersGridStore'); // TODO - НЕ РАБОТАЕТ??? ПОЧИНИТЬ
                            stor.removeAt(rowIndex); // спрятать строку TODO - НЕ РАБОТАЕТ??? ПОЧИНИТЬ
                            stor.load(); // TODO ПОКА СТРОЧКА ПРОСТО НЕ УДАЛЯЕТСЯ, ПЕРЕГРУЖАЕМ СТОРЭДЖ целиком
                        } else {
                            alert(Words_SystemErrorMsg + '106' + '\n' + Words_CallProgrammerMsg + '\n\n' + obj.message);
                        }
                    },
                    failure: function(response, opts) {
                        alert(Words_SystemErrorMsg + '106' + '\n' + Words_CallProgrammerMsg);
                    }
                });
            }
        }
    );
}

function RestoreUserById(rowIndex, UserId, Lastname) {
    Ext.Msg.confirm('Восстановление пользователя', 'Восстановить из архива пользователя № <b>' + UserId + '</b> (' + Lastname + ') ?',
        function(btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: RestoreUserUrl,
                    params  : {
                        UserId: UserId
                    },
                    success: function(response, opts) {
                        var obj = Ext.decode(response.responseText);
                        if(obj.success == true) {
                            // TODO если выбрана настройка "быстрый инет" - обновлять сторэдж, иначе не обновлять
                            var stor = Ext.data.StoreManager.lookup('UsersGridStore'); // TODO - НЕ РАБОТАЕТ??? ПОЧИНИТЬ
                            stor.removeAt(rowIndex); // спрятать строку TODO - НЕ РАБОТАЕТ??? ПОЧИНИТЬ
                            stor.load(); // TODO ПОКА СТРОЧКА ПРОСТО НЕ УДАЛЯЕТСЯ, ПЕРЕГРУЖАЕМ СТОРЭДЖ целиком
                        } else {
                            alert(Words_SystemErrorMsg + '107' + '\n' + Words_CallProgrammerMsg + '\n\n' + obj.message);
                        }
                    },
                    failure: function(response, opts) {
                        alert(Words_SystemErrorMsg + '107' + '\n' + Words_CallProgrammerMsg);
                    }
                });

            }
        }

    );
}

function GeneratePassword() {
    // придумать элементарный пароль
    var length   = 6,
        charset  = "abcdefghiknopqrstuvwxyz",
        ncharset = "123456789",
        retVal = "";
    for (var i = 3, n = ncharset.length; i < length; ++i) {
        retVal += ncharset.charAt(Math.floor(Math.random() * n));
    }
    for (var i = 0, n = charset.length; i < length-3; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    return retVal;
}




function OpenUserForms() {
    //Ext.getCmp('MainObjectsPanel').removeAll();

    //var OwnersGrid = new crm.view.Owners.OwnersGrid();

    var OwnersPanel = Ext.create('Ext.panel.Panel', {
        title: 'OwnersPanel',
        flex    : 1,
        //border: true,
        itemId : 'OwnersPanel',
        baseCls:'x-plain',
        layout: {
            type: 'hbox',
            align: 'stretch'
        }
    });

    var OwnersView = Ext.create('Ext.panel.Panel', {
        region  :'OwnersView',
        title   : 'OwnersView',
        itemId  : 'OwnersView',
        width   : 200,
        //html    : '<p>OwnersView!</p>',
        baseCls:'x-plain',
        flex    : 1,
        items   : [
            Ext.create('Ext.panel.Panel', {
                title       : 'Общие характеристики объекта',
                itemId      : 'OwnerObjInfo',
                html        : 'Информация по объекту',
                bodyPadding : 10
            }),
            Ext.create('Ext.panel.Panel', {
                title   : 'Телефон собственника, ссылка на источник',
                itemId  : 'OwnerObjPhone',
                html    : 'Телефон собственника, ссылка на источник',
                bodyPadding : 10
            }),
            Ext.create('Ext.panel.Panel', {
                title   : 'Описание',
                itemId  : 'OwnerObjDescr',
                html    : 'Описание',
                bodyPadding : 10
            })
        ]
    });

    Ext.ComponentQuery.query('#MainObjectsPanel')[0].add( OwnersPanel );
    //Ext.ComponentQuery.query('#OwnersPanel')[0].add( OwnersGrid );
    Ext.ComponentQuery.query('#OwnersPanel')[0].add( OwnersView );


    //OwnersGrid.store.setProxy( OwnersGridProxy ); // ставим url на активные объекты
    //OwnersGrid.store.load();

}


function BuildUserExportBtnUrlString(MainAjaxDriver, Action, Active, OnlyUserId, DownloadType, SortColumn, SortDir) {
    // аналог BuildObjectExportBtnUrlString()
    // делаем url для кнопки экспорта в Excel
    var url = MainAjaxDriver + '?' +
        '&Action='      + Action +
        '&Active='      + Active +
        '&OnlyUserId='  + OnlyUserId +
        '&DownloadType='+ DownloadType;
    if(typeof SortColumn !== "undefined") { // готовим параметры сортировки для совместимости с serverside сортировкой
        var SortObj         = new Object();
        SortObj.property    = SortColumn;
        SortObj.direction   = SortDir;
        url = url + '&sort='+JSON.stringify( Array(SortObj) );
    }
    console.log( arguments.callee.name + '('+Action+'): ' + url);
    return url;
}