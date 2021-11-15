
function SubmitClientForm(WindowName, ObjectTabsName, ClientFormName, ClientsGridName, ObjectAdditionsFormName, NeedToClose) {
    console.log(new Date() + ' '+arguments.callee.name + '('+WindowName+'...)');
    var ClientForm = Ext.getCmp(ClientFormName);
    ClientForm.getForm().submit({
        waitMsg:'Идет отправка...',
        success: function(form, action) {
            var Window = Ext.getCmp(WindowName);

            if(action.result.LoadedClientId > 0) {
                // Объект добавлен
                // обновляем заголовок окна
                Window.setTitle(Words_EditClientTitle + ' №' + action.result.LoadedClientId);

                // добавляем/обновляем в формы id нового/существующего объекта !!!!
                ClientForm.down('#LoadedClientId').setValue(action.result.LoadedClientId);
                // id для второй формы
                //Ext.getCmp(ObjectAdditionsFormName).down('#LoadedObjectId').setValue(action.result.LoadedObjectId); //#DBL

                // Открываем вкладку "Фотографии"
                // Меняем url, обновляем превьюшки по выбранному объекту
                //    UpdateObjectImagesDataView( action.result.LoadedObjectId, ObjectTabsName, GlobVars.OpenedRealtyType );
                //открываем вкладку "Дополнительно"
                //Ext.getCmp(ObjectTabsName).down('#'+ObjectAdditionsFormName).setDisabled(false);

            }

            // Окно успеха
            Ext.Msg.alert('Успех',  action.result.message);
            // TODO сделать обновление грида опциональным (т.к. для слабого канала может  - не требоваться)
            if(typeof Ext.getCmp(ClientsGridName) !== "undefined") { // грида может не быть при вызове окна через форму объекта
                Ext.getCmp(ClientsGridName).getStore().removeAll(); // TODO сделать обновление одной строки, а не всего грида!!!!
                Ext.getCmp(ClientsGridName).getStore().load();// обновляем весь грид
            }

            if(typeof Ext.ComponentQuery.query('#OwnerClientId')[0] !== "undefined") {
                // рядом есть открытая форма объекта в которой нужно обновить выбранного клиента
                var combo = Ext.ComponentQuery.query('#OwnerClientId')[0];
                combo.store.load();
                combo.reset();
                combo.store.clearFilter();
                combo.setValue(action.result.LoadedClientId.toString());
            }
            if(NeedToClose) {
                Window.close();
            }
        },
        failure: function(form, action) {
            switch (action.failureType) {
                case Ext.form.action.Action.CLIENT_INVALID:
                    Ext.Msg.show({
                        title   :'Ошибка',
                        msg     : 'Пожалуйста, заполните все обязательные поля!',
                        buttons : Ext.Msg.OK,
                        icon    : Ext.Msg.ERROR
                    });
                    break;
                case Ext.form.action.Action.CONNECT_FAILURE:
                    Ext.Msg.alert('Ошибка', 'CONNECT_FAILURE: проблема со связью');
                    break;
                case Ext.form.action.Action.SERVER_INVALID:
                    Ext.Msg.show({
                        title   :'Ошибка SERVER_INVALID:',
                        msg     : action.result.message,
                        buttons : Ext.Msg.OK,
                        icon    : Ext.Msg.ERROR
                    });
            }
        }
    });
}

function ArchivateClientById(rowIndex, ClientId, Lastname) {
    Ext.Msg.confirm('Архивация клиента', 'Отправить в архив клиента <b>' + Lastname + '</b> (№' + ClientId + ') ?',
        function(btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    type    : 'ajax',
                    url     : ArchivateClientUrl,
                    params  : {
                        ClientId: ClientId
                    },
                    success: function(response, opts) {
                        var obj = Ext.decode(response.responseText);
                        if(obj.success == true) {
                            // TODO если выбрана настройка "быстрый инет" - обновлять сторэдж, иначе не обновлять
                            var stor = Ext.data.StoreManager.lookup('ClientsGridStore'); // TODO - НЕ РАБОТАЕТ??? ПОЧИНИТЬ
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

function RestoreClientById(rowIndex, ClientId, Lastname) {
    Ext.Msg.confirm('Восстановление пользователя', 'Восстановить из архива пользователя № <b>' + ClientId + '</b> (' + Lastname + ') ?',
        function(btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: RestoreClientUrl,
                    params  : {
                        ClientId: ClientId
                    },
                    success: function(response, opts) {
                        var obj = Ext.decode(response.responseText);
                        if(obj.success == true) {
                            // TODO если выбрана настройка "быстрый инет" - обновлять сторэдж, иначе не обновлять
                            var stor = Ext.data.StoreManager.lookup('ClientsGridStore'); // TODO - НЕ РАБОТАЕТ??? ПОЧИНИТЬ
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


function BuildClientExportBtnUrlString(MainAjaxDriver, Action, Active, DownloadType, SortColumn, SortDir) {
    // аналог BuildObjectExportBtnUrlString()
    // делаем url для кнопки экспорта в Excel
    var url = MainAjaxDriver + '?' +
        '&Action='      + Action +
        '&Active='      + Active +
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