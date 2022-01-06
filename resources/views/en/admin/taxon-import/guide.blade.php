@extends('layouts.dashboard', ['title' => __('navigation.taxa_import')])

@section('content')
    <div class="box content">
        <h1>Field observations import guide</h1>

        <div class="message mt-8">
            <div class="message-body">
                Importing data into Birdloger from a table is complex process.
                Some errors that are not easy to handle could appear during this task.
                Entering observations this way is justified if you have a large set of data,
                which is not easy to type into Birdloger web interface. However, before
                you choose to import data from the table, think about all other options
                and study this manual in details, so we can avoid unnecessary complications.
            </div>
        </div>

        <h2>Table format</h2>

        <p>
            Birdloger works with tables saved in „.XLSX“ format.
        </p>

        <h2>Table content</h2>

        <p>
            Before importing the data from a table, you must correctly format the
            fields so that Birdloger can recognise the data. During the data import,
            Birdloger is reading values from table rows and columns, preforming a
            basic check of the data and putting the data into appropriate fields
            within the database. If you don’t format the field correctly, Birdloger
            will report an error and show the row within the table where this error
            occurred. In the worst case, Birdloger will not report the error and will
            put the imported data on a wrong place, which means that the Project
            team must react in order to correct this mistake.
        </p>

        <p>These are several things you should check before importing the data:</p>

        <ol>
            <li>
                It is necessary that items of import catalog are unique and does't exist in current Birdloger
                database. If there is match for either of the following fields: SPID, Birdlife sequence
                and/or Birdlife ID, whole import will fail.
            </li>

            <li>
                Field that contains values of true and false, as are: strictly protected, protected, priority
                list and referent list, must have values 'Yes' or 'Da' if they are true, respectively 'No', 'Ne'
                or empty field if they are false.
            </li>

            <li>
                Fields that have more than one value, like synonyms and annexes, they must be separated by
                semicolon <b>;</b> and white space <b>&#9251;</b> example: . "Anthropoides virgo; Grus virgo"
                i "1; 2b; 3a"
            </li>
        </ol>

        <p>Simple table serving as an example</p>

        <table>
            <thead>
            <tr>
                <th>Type</th>
                <th>SPID</th>
                <th>Species name</th>
                <th>Order</th>
                <th>Family</th>
                <th>Birdlife sequence</th>
                <th>Birdlife ID</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td>RS</td>
                <td>DENMAJO</td>
                <td>Dendrocopos major</td>
                <td>PICIFORMES</td>
                <td>Picidae</td>
                <td>3999</td>
                <td>22681124</td>
            </tr>

            <tr>
                <td>RS</td>
                <td>MERAPIA</td>
                <td>Merops apiaster</td>
                <td>CORACIIFORMES</td>
                <td>Meropidae</td>
                <td>3350</td>
                <td>22683756</td>
            </tr>

            <tr>
                <td>RS</td>
                <td>PICTRID</td>
                <td>Picoides tridactylus</td>
                <td>PICIFORMES</td>
                <td>Picidae</td>
                <td>3933</td>
                <td>22727137</td>
            </tr>
            </tbody>
        </table>

        <h2>Required/Unique fields</h2>

        <p>
            During the import, software will ask User for minimal set of data:
        </p>

        <dl>
            <dt>- Type</dt>
            <dd>Can be RS or WP value.</dd>
            <dt>- SPID</dt>
            <dd>Unique field. Combined first letters of species name.</dd>
            <dt>- Species name</dt>
            <dt>- Order</dt>
            <dt>- Family</dt>
            <dt>- Birdlife sequence</dt>
            <dd>Unique field.</dd>
            <dt>- Birdlife ID</dt>
            <dd>Unique field.</dd>
        </dl>

        <h2>From spreadsheet to database</h2>

        <p>
            No mater how we try to unify our tables, not a single data table will ever be the same.
            For this purpose it is required to tell Birdloger how every table looks like!
            This is done by simply choosing the columns and their order within the table.
        </p>

        <p>
            To start, click on a button "Select XLSX file" and choose already prepared
            table from your computer. If the first line of a table contain names of the columns,
            as in our example, you also need to check the field "First row contain column titles".
            After this, click on a button "Choose columns". You will notice that the list of
            columns in Birdloger is somewhat longer than the list of columns in your table
            and that five of them are already selected as required columns.
        </p>

        <p>
            If we would like to import the data from a table given in this example,
            we must select two additional columns: Observer and Identifier. Simply
            check this columns from the list and they will be selected for import.
            Since Birdloger can not guess the order of your columns in the table (from left to right),
            we will have to sort the columns in Birdloger list (from top to bottom) so
            that this order matches the table. You can do this simply by dragging the
            column names. In our example the right order of columns will be: Taxon,
            Longitude, Latitude, Year, Observer, Identifier, Data License.
        </p>

        <p>
            When you are sure that everything is OK (and when you checked everything
            for at least 10 times!), you may proceed to data import by pressing the Import button.
            The software will parse your table and, if everything is right, the records
            from the table will appear under your field observations.
        </p>
    </div>
@endsection

@section('breadcrumbs')
    <div class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="{{ route('contributor.index') }}">{{ __('navigation.dashboard') }}</a></li>
            <li><a href="{{ route('admin.taxa.index') }}">{{ __('navigation.taxa') }}</a></li>
            <li><a href="{{ route('admin.taxa-import.index') }}">{{ __('navigation.taxa_import') }}</a></li>
            <li class="is-active"><a>Uputstvo za uvoz</a></li>
        </ul>
    </div>
@endsection
