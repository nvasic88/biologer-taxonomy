@extends('layouts.dashboard', ['title' => __('navigation.taxa_import')])

@section('content')
    <div class="box content">
        <h1>Uputstvo za uvoz kataloga ptica</h1>

        <div class="message mt-8">
            <div class="message-body">
                Uvoz podataka iz tablice u Birdloger je kompleksan proces. Tom prilikom
                mogu da se potkradu greške koje nije jednostavno ispraviti. Unos podataka
                na ovaj način je opravdan ukoliko se radi o velikom setu podataka, koji nije
                jednostavno prekucati unutar veb okruženja Birdloger. Ipak, pre nego što se
                odlučite za uvoz podataka iz tabele razmislite o drugim mogućnostima i dobro
                proučite ovo uputstvo kako bi izbegli neželjene komplikacije.
            </div>
        </div>

        <h2>Format tabele</h2>

        <p>
            Birdloger radi sa excel tabelama koje su sačuvane u „.XLSX“ formatu.
        </p>

        <h2>Sadržaj tabele</h2>

        <p>
            Pre nego što uvezete podatke iz tabele morate pravilno formatirati njena
            polja kako bi Birdloger prepoznao podatke. Prilikom uvoza podataka Birdloger
            čita vrednosti iz redova i kolona tabele, sprovodi osnovnu proveru podataka
            i smešta podatke u odgovarajuća polja unutar baze podataka. Ukoliko ne formatirate
            polje kako treba, Birdloger će vam prijaviti grešku i ukazati u kom redu tablice
            se greška nalazi. U najgorem slučaju Birdloger neće prepoznati grešku već će
            podatak iz tablice smestiti na pogrešno mesto, nakon čega će biti neophodna
            intervencija našeg Projektnog tima.
        </p>

        <p>Evo nekoliko stvari koje najčešće morate uskladiti pre uvoza podataka:</p>

        <ol>
            <li>
                Neophodno je da stavke iz uvoznog kataloga budu jedinstvene i nepostojeće u trenutnoj
                Birdloger bazi. Ukoliko se desi da polja SPID, Birdlife sekvenca i/ili Birdlife ID
                već postoje u bazi za bilo koji red iz tabele, ceo uvoz će biti neuspešan.
            </li>

            <li>
                Polja koja sadrže vrednosti true/false, kao što su: striktno zaštićena, zaštićena, prioritetna
                lista i referentna lista moraju imati vrednosti Da ili Yes ukoliko su pozitivne, odnosno
                Ne, No ili prazna vrednost ukoliko su negativne.
            </li>

            <li>
                Polja koja imaju više od jedne vrednosti, kao što su: sinonimi i aneksi, moraju biti
                odvojeni znakom <b>;</b> i praznim razmakom <b>&#9251;</b> npr. "Anthropoides virgo; Grus virgo"
                i "1; 2b; 3a"
            </li>
        </ol>

        <p>Primer jedne proste tabele</p>

        <table>
            <thead>
                <tr>
                    <th>Tip</th>
                    <th>SPID</th>
                    <th>Naziv vrste</th>
                    <th>Red</th>
                    <th>Familija</th>
                    <th>Birdlife sekvenca</th>
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

        <h2>Obavezna polja / Jedinstvena polja</h2>

        <p>
            Prilikom uvoza podataka, od korisnika se traži minimalan set podataka:
        </p>

        <dl>
            <dt>- Tip</dt>
            <dd>Može biti RS ili WP.</dd>
            <dt>- SPID</dt>
            <dd>Jedinstveno polje. Sastavljen od prva slova naziva vrste.</dd>
            <dt>- Naziv vrste</dt>
            <dt>- Red</dt>
            <dt>- Familija</dt>
            <dt>- Birdlife sekvenca</dt>
            <dd>Jedinstveno polje.</dd>
            <dt>- Birdlife ID</dt>
            <dd>Jedinstveno polje.</dd>
        </dl>


        <h2>Od tabele do baze</h2>

        <p>
            Ma koliko se trudili da ujednačimo naše tablice, ni jedna tablica sa
            podacima nikada neće biti ista. Zbog toga je neophodno reći Birdlogeru
            kako izgleda svaka tabela ponaosob! To se vrši jednostavnim izborom
            kolona i njihovog redosleda.
        </p>

        <p>
            Kliknite na dugme „Odaberi XLSX datoteku“ i odaberite ranije pripremljenu
            tabelu sa vašeg računara. Ukoliko prvi red tablele sadrži nazive kolona,
            kao u našem primeru, potrebno je da čekirate polje „Prvi red sadrži nazive kolona“.
            Nakon toga kliknite na dugme „Odaberi kolone“. Primetićete da je spisak kolona
            nešto duži od onog koji sadrži vaša tablica, te da je pet kolona automatski
            označeno kao obavezne kolone.
        </p>

        <p>
            Ukoliko želimo da uvezemo podatke iz tabele koju smo dali kao primer,
            moraćemo da označimo još dve kolone: Posmatrač/i i Identifikovao. Jednostavno
            čekirajte još ove dve kolone sa spiska i one će biti označene za uvoz iz tablice.
            Pošto Birdloger ne može da zna redosled kolona u vašoj tablici (s leva na desno),
            moraćemo još da premestimo kolone u Birdlogeru (odozgo na dole) tako da odgovaraju tabeli.
            To možete uraditi jednostavnim prevlačenjem naziva kolona. U našem primeru moramo rasporediti
            kolone sledećim redosledom: Naziv vrste, Geografska dužina, Geografska širina, Godina, Posmatrač/i, Identifikovao, Licenca.
        </p>

        <p>
            Kada ste se uverili da je sve kako treba (i to 10 puta proverili!),
            možete kliknuti na dugme Uvezi. Program će obraditi vašu tablicu i,
            ako je sve kako treba, nalazi iz tablice će biti pridodati vašim terenskim nalazima.
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
