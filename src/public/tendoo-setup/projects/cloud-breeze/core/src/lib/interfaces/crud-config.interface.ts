import { Field } from "./field";

export interface TableColumn {
    [key: string ] : {
        label ?: string,
        replace ?: string;
        truncate ?: string;
        type ?: string
    },
}

export interface TableConfig {
    columns? : TableColumn;
    labels?  : {
        list_title : string;
        list_description : string;
        edit_title : string;
        edit_description : string;
        create_title : string;
        create_description : string;
    }
    fields: Field[];
    links: {[ key: string ]: string };
    results? : {[ key: string]: string | number | boolean }[];
    namespace?: string;
}

export interface TableAction {
    url: string;
    menu: {
        index: string;
        label: string;

        /**
         * namespace of the button
         */
        namespace: string;
        type: 'POST' | 'DELETE' | 'GET' | 'PUT',

        /**
         * raw url with the hastag
         */
        url: string
    }
}