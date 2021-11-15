
    function UpdateAdTarifByObjectId(ObjectId, TarifShortName, Value) {
        Ext.Ajax.request({
            url     : UpdateAdTarifUrl,
            params  : {
                ObjectId        : ObjectId,
                TarifShortName  : TarifShortName,
                Value           : Value
            },
            success: function(response, opts) {
                var obj = Ext.decode(response.responseText);
                if(obj.success == true) {
                    // work done
                } else {
                    alert(Words_SystemErrorMsg + '104' + '\n' + Words_CallProgrammerMsg + '\n\n' + obj.message);
                }
            },
            failure: function(response, opts) {
                alert(Words_SystemErrorMsg + '105' + '\n' + Words_CallProgrammerMsg);
            }
        });
    }

    function TrigerCianCheckboxes(rec, TrfName, checked) {
        // ф-я фактически выполняет функционал радиокнопкив 2х колонках циана
        if(TrfName == 'TrfCian') {
            rec.set('TrfCianPremium', false);
        } else if(TrfName == 'TrfCianPremium') {
            rec.set('TrfCian', false);
        }
    }

    function TrfCheckchangeEvent(GridName, TrfName, recordIndex, checked) {
        var rec = Ext.getCmp(GridName).getStore().getAt(recordIndex);
        //rec.set('TrfYandex', true);
        if(CheckUserAccessRule('Advert-All-Manage') ||
        (  CheckUserAccessRule('Advert-My-Manage') && rec.get('OwnerUserId') == GlobVars.CurrentUser.id ) ) {
            if( PermitActionByObjectColor(rec.get('Color')) ) {
                TrigerCianCheckboxes(rec, TrfName, checked);
                UpdateAdTarifByObjectId(rec.get('id'), TrfName, checked);
            } else {
                console.log('запрет по цвету, checked: ' + checked);
                if(checked == false) {
                    rec.set(TrfName, true); // если галка была установлена ранее, не трогаем её
                } else {
                    rec.set(TrfName, false); // запретить установку галки (установить пустой чекбокс)
                }
            }
        } else {
            rec.set(TrfName, false); // запретить установку галки
        }
    }

    function TrfRenderer(GridName, value, rowIndex) {
        // ф-я отрабатывается на каждой отрисовке чекбокса объекта!
        var out = null;
        var rec = Ext.getCmp(GridName).getStore().getAt(rowIndex);
        if(  CheckUserAccessRule('Advert-All-Manage') ||
            (CheckUserAccessRule('Advert-My-Manage') && rec.get('OwnerUserId') == GlobVars.CurrentUser.id ) ) {
            //console.log('renderer event');

            var cssPrefix = Ext.baseCSSPrefix,
                cls = cssPrefix + 'grid-checkcolumn';
            //metaData.tdCls += ' ' + this.disabledCls;
            if (value) {
                cls += ' ' + cssPrefix + 'grid-checkcolumn-checked';
            }
            out = '<img class="' + cls + '" src="' + Ext.BLANK_IMAGE_URL + '"/>';

        } else {
            // скрываем чекбокс
            //console.log( 'renderer 2' );
            //return '';
        }
        return out;
    }

    function AdColumnsTrigger(Action) {
        // Action: setEnabled/setDisabled, setVisible/setInvisible
        var TrfArr = new Array('TrfWinner','TrfAnSiteFree','TrfCian','TrfCianPremium','TrfAvito','TrfNavigatorFree','TrfRbcFree','TrfAfy','AdCosts','TrfYandex');

        if(Action == 'setVisible' || Action == 'setInvisible') {
            if(Action == 'setVisible') {
                var val = true;
            } else if(Action == 'setInvisible') {
                var val = false;
            }
            for(var i=0; i<TrfArr.length; i++) {
                if(typeof GlobVars.SysParams['TrfColumnEnabled_' + TrfArr[i]] !== "undefined") {
                    AdColumns_setVisible(TrfArr[i], val);
                }
            }
        }

        if(Action == 'setEnabled') {
            var CityGrid = Ext.getCmp('ObjectsGrid');
            var CountryGrid = Ext.getCmp('ObjectsCountryGrid');
            console.log('открываем рекламные галочки');
            if (typeof CityGrid !== "undefined") {
                for(var i=0; i<TrfArr.length; i++) {
                    if(typeof GlobVars.SysParams['TrfColumnEnabled_' + TrfArr[i]] !== "undefined") {
                        AdColumns_setDisabled(CityGrid, TrfArr[i], false);
                    }
                }
            }
            if (typeof CountryGrid !== "undefined") {
                for(var i=0; i<TrfArr.length; i++) {
                    if(typeof GlobVars.SysParams['TrfColumnEnabled_' + TrfArr[i]] !== "undefined") {
                        AdColumns_setDisabled(CountryGrid, TrfArr[i], false);
                    }
                }
            }
        }
    }

    function AdColumns_setVisible(ItemName, value) {
        if(typeof Ext.ComponentQuery.query( '#' + ItemName )[0] !== "undefined") {
            Ext.ComponentQuery.query( '#' + ItemName )[0].setVisible( value );
        } else {
            console.log('Error AdColumns_setVisible: не найден #'+ItemName);
        }
    }

    function AdColumns_setDisabled(Grid, ItemName, value) {
        if(typeof Grid.queryById( ItemName ) !== "undefined") {
            Grid.queryById( ItemName ).setDisabled( value );
        } else {
            console.log('Error AdColumns_setDisabled: не найден #'+ItemName);
        }


    }
