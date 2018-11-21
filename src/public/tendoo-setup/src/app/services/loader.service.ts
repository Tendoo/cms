import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

declare const tendoo;

@Injectable({
    providedIn: 'root'
})
export class LoaderService {
    isLoading   =   false;
    baseUrl     =   tendoo.base_url; 

    constructor(
        protected http: HttpClient
    ) { }

    /**
     * Submit post request
     * @param {string} url to access
     * @param data data to submit
     */
    post( url:string, data: { [ key:string] : any } ) {
        return new Observable( ( observer ) => {
            this.isLoading  =   true;
            return this.http.post( url, data ).subscribe( result => {
                this.isLoading  =   false;
                observer.next( result );
                observer.complete();
            }, error => {
                this.isLoading  =   false;
                observer.error( error );
            })
        });
    }

    /**
     * Submit get request
     * @param {string} url to access
     * @param data data to submit
     */
    get( url ) {
        return new Observable( ( observer ) => {
            this.isLoading  =   true;
            return this.http.get( url ).subscribe( result => {
                this.isLoading  =   false;
                observer.next( result );
                observer.complete();
            }, error => {
                this.isLoading  =   false;
                observer.error( error );
            })
        });
    }
}
