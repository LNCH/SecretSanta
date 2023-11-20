<x-mail::message>
# Hi {{ $player['displayName'] }}

Santa's elves have worked their magic, and have selected two of the Pickering
clan that you'll be buying gifts for this year!

The details of costs and types of gifts are all in the WhatsApp group, and so
without further ado, here are your selected names!

<x-mail::panel>
{{ collect($player['draws'])->map(fn ($draw) => ucwords($draw))->join(' and ') }}
</x-mail::panel>

Have fun shopping,<br>
Santa's Elves
</x-mail::message>
