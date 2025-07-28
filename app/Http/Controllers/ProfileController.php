<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the profile edit form
     */
    public function edit()
    {
        $countries = [
            'US' => 'United States',
            'CA' => 'Canada',
            'GB' => 'United Kingdom',
            'AU' => 'Australia',
            'DE' => 'Germany',
            'FR' => 'France',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'NL' => 'Netherlands',
            'BE' => 'Belgium',
            'CH' => 'Switzerland',
            'AT' => 'Austria',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'DK' => 'Denmark',
            'FI' => 'Finland',
            'PL' => 'Poland',
            'CZ' => 'Czech Republic',
            'HU' => 'Hungary',
            'RO' => 'Romania',
            'BG' => 'Bulgaria',
            'HR' => 'Croatia',
            'SI' => 'Slovenia',
            'SK' => 'Slovakia',
            'LT' => 'Lithuania',
            'LV' => 'Latvia',
            'EE' => 'Estonia',
            'IE' => 'Ireland',
            'PT' => 'Portugal',
            'GR' => 'Greece',
            'CY' => 'Cyprus',
            'MT' => 'Malta',
            'LU' => 'Luxembourg',
            'IS' => 'Iceland',
            'LI' => 'Liechtenstein',
            'MC' => 'Monaco',
            'SM' => 'San Marino',
            'VA' => 'Vatican City',
            'AD' => 'Andorra',
            'JP' => 'Japan',
            'CN' => 'China',
            'KR' => 'South Korea',
            'IN' => 'India',
            'BR' => 'Brazil',
            'MX' => 'Mexico',
            'AR' => 'Argentina',
            'CL' => 'Chile',
            'CO' => 'Colombia',
            'PE' => 'Peru',
            'VE' => 'Venezuela',
            'EC' => 'Ecuador',
            'BO' => 'Bolivia',
            'PY' => 'Paraguay',
            'UY' => 'Uruguay',
            'GY' => 'Guyana',
            'SR' => 'Suriname',
            'FK' => 'Falkland Islands',
            'GF' => 'French Guiana',
            'RU' => 'Russia',
            'UA' => 'Ukraine',
            'BY' => 'Belarus',
            'MD' => 'Moldova',
            'GE' => 'Georgia',
            'AM' => 'Armenia',
            'AZ' => 'Azerbaijan',
            'KZ' => 'Kazakhstan',
            'UZ' => 'Uzbekistan',
            'KG' => 'Kyrgyzstan',
            'TJ' => 'Tajikistan',
            'TM' => 'Turkmenistan',
            'AF' => 'Afghanistan',
            'PK' => 'Pakistan',
            'BD' => 'Bangladesh',
            'LK' => 'Sri Lanka',
            'NP' => 'Nepal',
            'BT' => 'Bhutan',
            'MV' => 'Maldives',
            'MY' => 'Malaysia',
            'SG' => 'Singapore',
            'TH' => 'Thailand',
            'VN' => 'Vietnam',
            'PH' => 'Philippines',
            'ID' => 'Indonesia',
            'MM' => 'Myanmar',
            'LA' => 'Laos',
            'KH' => 'Cambodia',
            'BN' => 'Brunei',
            'TL' => 'Timor-Leste',
            'MN' => 'Mongolia',
            'KP' => 'North Korea',
            'TW' => 'Taiwan',
            'HK' => 'Hong Kong',
            'MO' => 'Macau',
            'TR' => 'Turkey',
            'SA' => 'Saudi Arabia',
            'AE' => 'United Arab Emirates',
            'QA' => 'Qatar',
            'KW' => 'Kuwait',
            'BH' => 'Bahrain',
            'OM' => 'Oman',
            'YE' => 'Yemen',
            'JO' => 'Jordan',
            'LB' => 'Lebanon',
            'SY' => 'Syria',
            'IQ' => 'Iraq',
            'IR' => 'Iran',
            'IL' => 'Israel',
            'PS' => 'Palestine',
            'EG' => 'Egypt',
            'LY' => 'Libya',
            'TN' => 'Tunisia',
            'DZ' => 'Algeria',
            'MA' => 'Morocco',
            'SD' => 'Sudan',
            'SS' => 'South Sudan',
            'ET' => 'Ethiopia',
            'ER' => 'Eritrea',
            'DJ' => 'Djibouti',
            'SO' => 'Somalia',
            'KE' => 'Kenya',
            'UG' => 'Uganda',
            'TZ' => 'Tanzania',
            'RW' => 'Rwanda',
            'BI' => 'Burundi',
            'CD' => 'Democratic Republic of the Congo',
            'CG' => 'Republic of the Congo',
            'GA' => 'Gabon',
            'GQ' => 'Equatorial Guinea',
            'CM' => 'Cameroon',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'BF' => 'Burkina Faso',
            'ML' => 'Mali',
            'SN' => 'Senegal',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'SL' => 'Sierra Leone',
            'LR' => 'Liberia',
            'CI' => 'Ivory Coast',
            'GH' => 'Ghana',
            'TG' => 'Togo',
            'BJ' => 'Benin',
            'ST' => 'Sao Tome and Principe',
            'CV' => 'Cape Verde',
            'GM' => 'Gambia',
            'MZ' => 'Mozambique',
            'ZW' => 'Zimbabwe',
            'ZM' => 'Zambia',
            'MW' => 'Malawi',
            'BW' => 'Botswana',
            'NA' => 'Namibia',
            'SZ' => 'Eswatini',
            'LS' => 'Lesotho',
            'ZA' => 'South Africa',
            'MG' => 'Madagascar',
            'MU' => 'Mauritius',
            'SC' => 'Seychelles',
            'KM' => 'Comoros',
            'MV' => 'Maldives',
            'NZ' => 'New Zealand',
            'FJ' => 'Fiji',
            'PG' => 'Papua New Guinea',
            'SB' => 'Solomon Islands',
            'VU' => 'Vanuatu',
            'NC' => 'New Caledonia',
            'PF' => 'French Polynesia',
            'TO' => 'Tonga',
            'WS' => 'Samoa',
            'KI' => 'Kiribati',
            'TV' => 'Tuvalu',
            'NR' => 'Nauru',
            'PW' => 'Palau',
            'MH' => 'Marshall Islands',
            'FM' => 'Micronesia',
            'CK' => 'Cook Islands',
            'NU' => 'Niue',
            'TK' => 'Tokelau',
            'AS' => 'American Samoa',
            'GU' => 'Guam',
            'MP' => 'Northern Mariana Islands',
            'PR' => 'Puerto Rico',
            'VI' => 'U.S. Virgin Islands',
            'AI' => 'Anguilla',
            'AG' => 'Antigua and Barbuda',
            'AW' => 'Aruba',
            'BS' => 'Bahamas',
            'BB' => 'Barbados',
            'BZ' => 'Belize',
            'BM' => 'Bermuda',
            'VG' => 'British Virgin Islands',
            'KY' => 'Cayman Islands',
            'CR' => 'Costa Rica',
            'CU' => 'Cuba',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'SV' => 'El Salvador',
            'GD' => 'Grenada',
            'GT' => 'Guatemala',
            'HT' => 'Haiti',
            'HN' => 'Honduras',
            'JM' => 'Jamaica',
            'NI' => 'Nicaragua',
            'PA' => 'Panama',
            'KN' => 'Saint Kitts and Nevis',
            'LC' => 'Saint Lucia',
            'VC' => 'Saint Vincent and the Grenadines',
            'TT' => 'Trinidad and Tobago',
            'TC' => 'Turks and Caicos Islands',
        ];

        return view('profile.edit', [
            'user' => auth()->user(),
            'countries' => $countries
        ]);
    }

    /**
     * Update the user's profile
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'country' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = $request->only(['name', 'email', 'phone', 'gender', 'date_of_birth', 'country', 'bio']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
                        // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('profile.edit')->with('success', 'Password updated successfully!');
    }
}
