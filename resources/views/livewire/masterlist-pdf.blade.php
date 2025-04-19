<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">First Name</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Middle Name</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Family Name</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Name Extension</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Barangay</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Precinct</th>
        </tr>
    </thead>
    <tbody>
        @forelse($masterList as $leader)
            @if($leader['leader'])
                <!-- Leader row -->
                <tr style="background-color: #eef; font-weight: bold;">
                    <td style="border:1px solid #ddd; padding:8px; text-transform: uppercase;">
                        {{ strtoupper($leader['firstName']) }}
                    </td>
                    <td style="border:1px solid #ddd; padding:8px; text-transform: uppercase;">
                        {{ strtoupper($leader['middleName']) }}
                    </td>
                    <td style="border:1px solid #ddd; padding:8px; text-transform: uppercase;">
                        {{ strtoupper($leader['familyName']) }}
                    </td>
                    <td style="border:1px solid #ddd; padding:8px; text-transform: uppercase;">
                        {{ strtoupper($leader['nameExtension']) }}
                    </td>
                    <td style="border:1px solid #ddd; padding:8px; text-transform: uppercase;">
                        {{ strtoupper($leader['barangay']) }}
                    </td>
                    <td style="border:1px solid #ddd; padding:8px; text-transform: uppercase;">
                        {{ strtoupper($leader['precinctNo']) }} — LEADER
                    </td>
                </tr>
            @endif

            @if(empty($leader['members']))
                <tr>
                    <td colspan="6" style="border:1px solid #ddd; padding:8px; text-align:left; padding-left:32px; color:#666; text-transform: uppercase;">
                        NO MEMBERS ASSIGNED.
                    </td>
                </tr>
            @else
                @foreach($leader['members'] as $member)
                    <tr>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; padding-left:32px; text-transform: uppercase;">
                            {{ strtoupper($member['firstName']) }}
                        </td>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; text-transform: uppercase;">
                            {{ strtoupper($member['middleName']) }}
                        </td>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; text-transform: uppercase;">
                            {{ strtoupper($member['familyName']) }}
                        </td>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; text-transform: uppercase;">
                            {{ strtoupper($member['nameExtension']) }}
                        </td>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; text-transform: uppercase;">
                            {{ strtoupper($member['barangay']) }}
                        </td>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; text-transform: uppercase;">
                            {{ strtoupper($member['precinctNo']) }}
                        </td>
                    </tr>
                @endforeach
            @endif
        @empty
            <tr>
                <td colspan="6" style="border:1px solid #ddd; padding:8px; text-align:center; text-transform: uppercase;">
                    NO RECORDS FOUND
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
