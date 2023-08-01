/**
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 */
import { ClassicEditor as ClassicEditorBase } from '@ckeditor/ckeditor5-editor-classic';
import { Enter } from '@ckeditor/ckeditor5-enter';
import { Essentials } from '@ckeditor/ckeditor5-essentials';
import { UploadAdapter } from '@ckeditor/ckeditor5-adapter-ckfinder';
import { Alignment } from '@ckeditor/ckeditor5-alignment';
import { Autoformat } from '@ckeditor/ckeditor5-autoformat';
import { Bold, Italic } from '@ckeditor/ckeditor5-basic-styles';
import { BlockQuote } from '@ckeditor/ckeditor5-block-quote';
import { CKBox } from '@ckeditor/ckeditor5-ckbox';
import { CKFinder } from '@ckeditor/ckeditor5-ckfinder';
import { EasyImage } from '@ckeditor/ckeditor5-easy-image';
import { Heading } from '@ckeditor/ckeditor5-heading';
import { Image, ImageCaption, ImageStyle, ImageToolbar, ImageUpload, PictureEditing } from '@ckeditor/ckeditor5-image';
import { Indent } from '@ckeditor/ckeditor5-indent';
import { Link } from '@ckeditor/ckeditor5-link';
import { List } from '@ckeditor/ckeditor5-list';
import { MediaEmbed } from '@ckeditor/ckeditor5-media-embed';
import { Paragraph } from '@ckeditor/ckeditor5-paragraph';
import { PasteFromOffice } from '@ckeditor/ckeditor5-paste-from-office';
import { Table, TableToolbar } from '@ckeditor/ckeditor5-table';
import { TextTransformation } from '@ckeditor/ckeditor5-typing';
import { CloudServices } from '@ckeditor/ckeditor5-cloud-services';
//export default class ClassicEditor extends ClassicEditorBase {
//     static builtinPlugins: (typeof TextTransformation | typeof Essentials | typeof UploadAdapter | typeof Paragraph | typeof Heading | typeof Autoformat | typeof Bold | typeof Italic | typeof BlockQuote | typeof CloudServices | typeof Image | typeof ImageCaption | typeof ImageStyle | typeof ImageToolbar | typeof ImageUpload | typeof CKBox | typeof CKFinder | typeof EasyImage | typeof Indent | typeof Link | typeof List | typeof MediaEmbed | typeof PasteFromOffice | typeof Table | typeof TableToolbar | typeof PictureEditing)[];
//     static defaultConfig: {
//         toolbar: {
//             items: string[];
//         };
//         image: {
//             toolbar: string[];
//         };
//         table: {
//             contentToolbar: string[];
//         };
//         language: string;
//     };
// }
export default class ClassicEditor extends ClassicEditorBase {
    static builtinPlugins: 
    (typeof Alignment | typeof Enter | typeof TextTransformation | typeof Essentials | typeof UploadAdapter | typeof Paragraph | typeof Heading | typeof Autoformat | typeof Bold | typeof Italic | typeof BlockQuote | typeof CloudServices | typeof Image | typeof ImageCaption | typeof ImageStyle | typeof ImageToolbar | typeof ImageUpload | typeof CKBox | typeof CKFinder | typeof EasyImage | typeof Indent | typeof Link | typeof List | typeof MediaEmbed | typeof PasteFromOffice | typeof Table | typeof TableToolbar | typeof PictureEditing)[];

    // Editor configuration.
    static defaultConfig: {
        toolbar: {
            items: [
                'alignment',  // Displaying the proper UI element in the toolbar.
                'heading',
                '|',
                'bold',
                'italic',
                'link',
                'bulletedList',
                'numberedList',
                '|',
                'outdent',
                'indent',
                '|',
                'imageUpload',
                'blockQuote',
                'insertTable',
                'mediaEmbed',
                'undo',
                'redo'
            ]
        },
        language: 'en',
        image: {
            toolbar: [
                'imageTextAlternative',
                'toggleImageCaption',
                'imageStyle:inline',
                'imageStyle:block',
                'imageStyle:side'
            ]
        },
        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells'
            ]
        }
    };
}