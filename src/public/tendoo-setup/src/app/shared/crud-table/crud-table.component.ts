import { Component, OnInit, Input, Output, OnDestroy } from '@angular/core';
import { TableConfig } from 'src/app/interfaces/table-config.interface';
import { TableColumnInterface } from 'src/app/interfaces/table-column.interface';
import { TableEntryInterface } from 'src/app/interfaces/table-entry.interface';
import { EventEmitter } from '@angular/core';
import { MatDialog, MatSnackBar } from '@angular/material';
import { ConfirmDialogComponent } from '../confirm-dialog/confirm-dialog.component';
import { ConfirmDialogObject } from 'src/app/interfaces/confirm-dialog';
import { CoreAction } from 'src/app/interfaces/core-action';
import { Router } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { AsyncResponse } from 'src/app/interfaces/async-response';
import { TendooService } from 'src/app/services/tendoo.service';
import { CrudConfig, CrudTableColumn } from 'src/app/interfaces/crud-config.interface';
import { CoreEvent } from 'src/app/classes/core-event.class';
import { MediaObserver } from '@angular/flex-layout';

@Component({
    selector: 'app-crud-table',
    templateUrl: './crud-table.component.html',
    styleUrls: ['./crud-table.component.css']
})
export class CrudTableComponent implements OnInit, OnDestroy {
    @Input( 'crud' ) crud: CrudConfig;
    
    @Output( 'sort' ) sort              =   new EventEmitter();
    @Output( 'delete' ) delete          =   new EventEmitter();
    @Output( 'action' ) action          =   new EventEmitter();
    @Output( 'search' ) searchEvent     =   new EventEmitter();

    columnsNames: string[]                  =   [];
    searchEnabled                           =   false;
    reservedColumns: string[]               =   [ '$actions' ];
    searchValue                             =   '';
    checkAll: any;
    columns: CrudTableColumn;
    noResponseMsg: string                   =   'The action has returned no response';
    
    constructor(
        private dialog: MatDialog,
        private router: Router,
        private snackbar: MatSnackBar,
        public tendoo: TendooService,
        private coreEvent: CoreEvent,
    ) { }
    
    ngOnInit() {
        this.columnsNames       =   Object.keys( this.crud.columns );
        this.columns            =   this.crud.columns;

        this.coreEvent.subscribe( (action: CoreAction ) => {
            if ([ 'crud.action.success', 'crud.action.failed'].includes( action.type ) ) {
                this.snackbar.open( 
                    action.data.message ? action.data.message : this.noResponseMsg, 
                    'OK', 
                    { duration: 5000 }
                );

                /**
                 * the menu namespace should be 
                 * provided on the action data under the "menu" key
                 */
                this.dialog.getDialogById( action.data.menu.namespace ).close();
            } 

            if([ 'crud.bulk.success', 'crud.bulk.failed' ].includes( action.type ) ) {
                this.snackbar.open( 
                    action.data.message ? action.data.message : this.noResponseMsg, 
                    'OK', 
                    { duration: 3000 }
                );

                /**
                 * the menu namespace should be 
                 * provided on the action data under the "menu" key
                 */
                this.dialog.getDialogById( 'bulk.action' ).close();
            }
        });
    }

    ngOnDestroy() {
    }

    sortData( event ) {
        this.sort.emit( event );
    }

    search( field: HTMLInputElement ) {
        if ( field.value.length !== 0 ) {
            this.searchEvent.emit( 
                field.value.length
            );
        } 
        return this.snackbar.open( 'You need to input something to search.', 'OK', { duration: 3000 });
    }

    /**
     * trigger menu
     * @param object menu
     * @return void
     */
    triggerMenu( menu, row ) {
        /**
         * build the url based on the 
         * configuration.
         */
        const url   =   menu.url.replace( "#", row[ menu.index || 'id' ] );

        if ( menu.confirm !== undefined ) {
            this.dialog.open( ConfirmDialogComponent, {
                id: menu.namespace,
                data: <ConfirmDialogObject>{
                    title: menu.confirm.title,
                    message: menu.confirm.message,
                    buttons: [
                        {
                            label: 'Ok',
                            namespace: 'ok',
                            onClick: () => {
                                this.__proceedAction( menu, url );
                            }
                        }, {
                            label: 'Cancel',
                            namespace: 'cancel',
                            onClick: () => {
                                this.dialog.getDialogById( menu.namespace )
                                    .close();
                            }
                        }
                    ]
                }
            });
        } else {
            this.__proceedAction( menu, url );
        }
    }
    
    /**
     * Proceed action after having
     * checked if that action require a confirmation
     * @param object menu
     * @return void
     */
    private __proceedAction( menu, url ) {
        this.action.emit({ menu, url });
    }

    /**
     * Make sure to toggle all
     * checkboxes when the main checkbox
     * is clicked
     * @return void
     */
    checkAllCheckboxes() {
        this.crud.results[ 'data' ].forEach( checkbox => {
            checkbox.$checked       =   this.checkAll;
        });
    }

    /**
     * delete selected entries
     * @param entries
     * @return void
     */
    deleteSelectedEntries( entries ) {
        this.dialog.open( ConfirmDialogComponent, {
            id: 'bulk.action',
            data: <ConfirmDialogObject>{
                title: 'Please Confirm Your Action',
                message: 'Would you like to delete all the selected users ? This action can\'t be canceled !',
                buttons: [
                    {
                        label: 'Delete',
                        namespace: 'delete.selected',
                        onClick: () => {
                            this.delete.emit({
                                entries: this.selectedEntries,
                                dialog: { 
                                    id: 'bulk.action'
                                }
                            });
                        }
                    }, {
                        label: 'Cancel',
                        namespace: 'cancel',
                        onClick: () => {
                            this.dialog.getDialogById( 'bulk.action' ).close();
                        }
                    }
                ]
            }
        })
    }
    
    /**
     * canceled all selected entries
     * @return void
     */
    cancel() {
        this.crud.results[ 'data' ].forEach( entry => entry.$checked = false );
        this.checkAll   =   false;
    }

    /**
     * toggle search. Enable /disable according
     * to the provided parameter
     * @return void
     */
    toggleSearch( param: boolean ) {
        if ( param ) {
            this.searchEnabled = true;
            setTimeout( () => {
                document.getElementById( 'search-field' ).focus();
            }, 100 );
        } else {
            this.searchEnabled  =   false;
            this.searchValue    =   '';
        }
    }

    /**
     * get if it has a selected entries
     * @return boolean
     */
    get hasSelectedEntries(): boolean {
        return this.selectedEntries.length > 0;
    }

    /**
     * return selected entries
     * @return array
     */
    get selectedEntries() {
        return this.crud.results[ 'data' ] !== undefined ? this.crud.results[ 'data' ].filter( entry => entry.$checked ) : [];
    }    
}
