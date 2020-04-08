import { NgModule } from "@angular/core";
import { CrudTableComponent } from "./components/crud-table/crud-table.component";
import { DialogComponent } from "./components/dialog/dialog.component";
import { FieldsComponent } from "./components/fields/fields.component";
import { LoadingAnimationComponent } from "./components/loading-animation/loading-animation.component";
import { MenuListComponent } from "./components/menu-list/menu-list.component";
import { SubMenuListComponent } from "./components/sub-menu-list/sub-menu-list.component";
import { MaterialModule } from "./modules/material.module";
import { FlexLayoutModule } from "@angular/flex-layout";
import { NgxCaptchaModule } from "ngx-captcha";
import { CommonModule } from "@angular/common";
import { FormsModule, ReactiveFormsModule } from "@angular/forms";
import { ImageCropperModule } from "ngx-image-cropper";
import { RouterModule } from "@angular/router";
import { TruncatePipe } from "./pipes/truncate.pipe";
import { TruncateMiddlePipe } from "./pipes/truncate-middle.pipe";
import { DpDatePickerModule } from 'ng2-date-picker';

@NgModule({
    declarations: [
        CrudTableComponent,
        DialogComponent,
        FieldsComponent,
        LoadingAnimationComponent,
        MenuListComponent,
        SubMenuListComponent,
        TruncatePipe,
        TruncateMiddlePipe
    ], 
    imports: [
        MaterialModule,
        FlexLayoutModule,
        NgxCaptchaModule,
        CommonModule,
        FormsModule,
        ReactiveFormsModule,
        ImageCropperModule,
        RouterModule,
        DpDatePickerModule,
    ],
    exports: [
        MaterialModule,
        FieldsComponent,
		CrudTableComponent,
		DialogComponent,
		LoadingAnimationComponent,
		MenuListComponent,
		SubMenuListComponent,
    ]
})
export class CoreModule {}