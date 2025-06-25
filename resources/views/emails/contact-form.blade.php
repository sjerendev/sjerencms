<x-mail::message>
# Nytt kontaktformulär

**Namn:** {{ $formData['name'] }}

**E-post:** {{ $formData['email'] }}

**Gällande:** {{ $formData['subject'] }}

**Meddelande:**
{{ $formData['message'] }}

</x-mail::message>
