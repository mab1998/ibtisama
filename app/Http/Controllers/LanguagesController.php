<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\RedirectIfNotParmitted;
use App\Models\EmailTemplate;
use App\Models\Language;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class LanguagesController extends Controller {
    public function __construct(){
        $this->middleware(RedirectIfNotParmitted::class.':language');
    }

    public function index(){
        return Inertia::render('Languages/Index', [
            'title' => 'Languages',
            'filters' => Request::all('search'),
        ]);
    }

    public function create() {
        return Inertia::render('Languages/Create',[
            'title' => 'Add a new language',
        ]);
    }

    public function store() {
        $data = Request::validate([
            'name' => ['required', 'max:50'],
            'code' => ['required', 'max:3'],
        ]);
        File::put(lang_path($data['code'].'.json'), \json_encode([]));
        Language::create($data);

        return Redirect::route('languages')->with('success', 'Language created.');
    }

    public function delete($language){
        if (config('app.demo')) {
            return Redirect::back()->with('error', 'Deleting language is not allowed for the live demo.');
        }
        $language = Language::where('id', $language)->first();
        if(!empty($language)){
            if(File::exists(lang_path($language->code.'.json'))){
                File::delete(lang_path($language->code.'.json'));
            }
            $language->delete();
            return Redirect::back()->with('success', 'Language deleted!');
        }else{
            return Redirect::back()->with('error', 'Can not delete the language!');
        }
    }

    public function newItem(){
        $languageItems = Request::input('new_data');
        $origin = $languageItems['en'];
        foreach ($languageItems as $languageItemKey => $languageItemValue){
            $language_file = lang_path($languageItemKey . '.json');
            $decoded_file = json_decode(file_get_contents($language_file), true);
            $decoded_file[$origin] = $languageItemValue;
            file_put_contents($language_file, json_encode($decoded_file, JSON_UNESCAPED_UNICODE));
        }
        return Redirect::back()->with('success', 'Language data added!');
    }

    public function deleteItem($value){
        if (config('app.demo')) {
            return Redirect::back()->with('error', 'Deleting language is not allowed for the live demo.');
        }elseif($value == 'en'){
            return Redirect::back()->with('error', 'You can not delete the default(english) language.');
        }
        $languageItems = Language::get();
        foreach ($languageItems as $languageItem){
            $language_file = lang_path($languageItem->code . '.json');
            $decoded_file = json_decode(file_get_contents($language_file), true);
            unset($decoded_file[$value]);
            file_put_contents($language_file, json_encode($decoded_file, JSON_UNESCAPED_UNICODE));
        }
        return Redirect::back()->with('success', 'Language item has been deleted!');
    }

    public function edit(Language $language){
        if (config('app.demo')) {
            return Redirect::back()->with('error', 'Updating language is not allowed for the live demo.');
        }
        $language_file = lang_path($language->code . '.json');
        $decoded_file = json_decode(file_get_contents($language_file), true);
        $languageData = [];
        foreach ($decoded_file as $dfk => $dfv){
            $languageData[] = ['name' => $dfk, 'value' => $dfv];
        }
        return Inertia::render('Languages/Edit', [
            'title' => $language->name,
            'languages' => Language::get(),
            'language_data' => [
                'id' => $language->id,
                'name' => $language->name,
                'code' => $language->code,
                'data' => $languageData,
            ],
        ]);
    }

    public function update(Language $language) {
        $languageData = Request::input('language_values');

        $decodedData = [];
        foreach ($languageData as $dataValue){
            $decodedData[$dataValue['name']] = $dataValue['value'];
        }

        $languagePath = lang_path($language->code . '.json');
        file_put_contents($languagePath, json_encode($decodedData, JSON_UNESCAPED_UNICODE));

        return Redirect::back()->with('success', 'Language data updated!');
    }

    public function newLanguageManually($code){
        $language_file = lang_path($code . '.json');
        $decoded_file = json_decode(file_get_contents($language_file), true);
//        print_r(implode(',', array_keys($decoded_file)));exit;
//        dd();
//        dd($decoded_file);
        $phpString = "Edytuj profil||Panel||Wyloguj||Bilety||Czat||Często zadawane pytania||Bloga||Baza wiedzy||Więcej||Notatki||Łączność||Organizacje||Użytkownicy||Klienci||Ustawienia||Światowy||Kategorie||Status||Priorytety||Działy||Typy||Szablony wiadomości||Poczta SMTP||Popychacz||Czat Pushera||Pierwsze strony||Kontakt||Usługi||Polityka prywatności||Warunki korzystania z usług||Filtr||Zniszczone||Zniszczone z||Tylko do kosza||Szukaj...||Resetowanie||Nazwa||E-mail||Telefon||Kraj||Stwórz użytkownika||Imię||Imię||Nazwisko||Nazwisko||Miasto||Adres||Hasło||Rola||Zdjęcie||Nowe bilety||Bilety otwarte||Zamknięte bilety||Nieprzypisane bilety||Bilet według działu||Bilet według rodzaju||Najlepszy twórca biletów||Historia biletów||Czas pierwszej reakcji||Czas ostatniej odpowiedzi||Techniczny||Sprzęt komputerowy||Rozwój||Kierownictwo||Admin||Oprogramowanie||Praca||Wydarzenie||Przeciętny||sekundy||ten miesiąc||w zeszłym miesiącu||Miesiąc||Miesiące||Dzień||Dni||godziny||Godzina||Minuty||Minuta||Klucz||Temat||Dołącz pliki||Priorytet||Data||Zaktualizowano||Klient||Dział||Przypisane do||Typ biletu||Kategoria||Utworzony||Szczegóły żądania||Załącz plik||Usuń bilet||Ratować||Dyskusja na temat biletów||Historie komentarzy do tego zgłoszenia będą dostępne tutaj.||Bilet||Historie komentarzy||Historia komentarzy||Napisz komentarz i naciśnij Enter, aby wysłać...||Kliknij rozmowę po lewej stronie, aby zobaczyć jej historię.||Wpisz wiadomość...||Często zadawane pytania||Utwórz bilet||Nowy bilet||Utwórz często zadawane pytania||Filtruj według priorytetu||Filtruj według stanu||Filtruj według roli||Usuń często zadawane pytania||Aktualizuj często zadawane pytania||Utwórz bazę wiedzy||Tytuł||Typ||Detale||Usuń bazę wiedzy||Zaktualizuj bazę wiedzy||Notatka||Nie znaleziono biletu.||zanotuj szczegóły tutaj...||Składać||Anulować||Usuń notatkę||Stworzyć kontakt||Organizacja||Usuń kontakt||Aktualizuj kontakt||Utwórz organizację||Województwo||Państwo||Kod pocztowy||Usuń organizację||Aktualizuj organizację||Usuwać||Aktualizacja||Tworzyć||Utwórz Klienta||Zarządzaj użytkownikami||Usuń użytkownika||Aktualizuj użytkownika||Czy na pewno chcesz usunąć tego użytkownika?||Nazwa aplikacji||Domyślny język||powiadomienia e-mailowe||Utwórz zgłoszenie przez nowego klienta||Utwórz bilet z poziomu pulpitu nawigacyjnego||Powiadomienie o pierwszym komentarzu||Użytkownik został przydzielony do zadania||Zmiany statusu lub priorytetu||Utwórz nowego użytkownika||Instrukcja pracy Cron||Jeśli chcesz wysyłać pocztę bez opóźnień, musisz ustawić do tego zadanie cron z częstotliwością raz na minutę.||Utwórz kategorię||Tworzyć nowe||Statusy||Utwórz status||Ślimak||Utwórz priorytet||Utwórz dział||Utwórz typ||Szablon e-maila||Wyślij e-mailem HTML||Aktualizuj szablon||Host SMTP||Port SMTP||Nazwa użytkownika SMTP||Hasło SMTP||Szyfrowanie poczty||Z adresu||Z nazwy||Identyfikator aplikacji Pusher||Klucz aplikacji Pusher||Sekret aplikacji Pusher||Klaster aplikacji Pusher||Lokalizacja||Numer telefonu||Adres e-mail||Adres e-mail||Adres lokalizacji||Szczegóły e-mailem||Szczegóły lokalizacji||Mapa lokalizacji||Dodaj nowe||Ikona||Etykietka||Prywatność||Informacje o liście||Spód||Lista przedmiotów||Zawartość strony||Często Zadawane Pytania||Skontaktuj się z nami||Przydatne łącze||Firma||Subskrybuj||Zaloguj sie||Przedstawić bilet||Zaloguj się do HelpDesku||Często zadawane pytania||Filtruj bilety według||Przypisać do||Nie znaleziono rozmowy.||Nie znaleziono najczęściej zadawanych pytań.||Opis często zadawanych pytań||Aktywny||Nieaktywny||Przeglądać||Wiedza||Języki||Użytkownik||Nie znaleziono języków.||Role użytkowników||Nie znaleziono organizacji.||Dom||Utwórz bilet||Otwórz bilet||Wybierz kategorię||Wybierz typ||Wybierz dział||Rozpocznij czat||Potwierdź hasło||Nie znaleziono żadnych klientów.||Nie znaleziono bazy wiedzy.||Utwórz post||Obraz funkcji||Wybierz rodzaj||Utwórz nowy post||Nie znaleziono bazy postów.||Jest aktywny||Wszystkie posty||Wyszukaj swoje zapytanie w często zadawanych pytaniach...";
        $languageData = [];
        $inc = 0;
        $phpStringArr = explode('||',$phpString);
        foreach ($decoded_file as $dfk => $dfv){
//            print_r($dfk);
//            echo "<br>";
//            echo "<br>";
//            $languageData[] = ['name' => $dfk, 'value' => $dfv];
            $languageData[$dfk] = $phpStringArr[$inc];
            $inc+=1;
        }
        file_put_contents($language_file, json_encode($languageData, JSON_UNESCAPED_UNICODE));
        dd($languageData);
        exit;
        return Inertia::render('Languages/Edit', [
            'title' => $language->name,
            'languages' => Language::get(),
            'language_data' => [
                'id' => $language->id,
                'name' => $language->name,
                'code' => $language->code,
                'data' => $languageData,
            ],
        ]);
    }
}
