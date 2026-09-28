@php
    $fontFamily = $fontFamily ?? 'Helvetica, Arial, sans-serif';
    $sz8 = $sz8 ?? '8px';
    $sz8_5 = $sz8_5 ?? '8.5px';
    $sz9 = $sz9 ?? '9px';
    $sz9_5 = $sz9_5 ?? '9.5px';
    $sz10 = $sz10 ?? '10px';
    $sz10_5 = $sz10_5 ?? '10.5px';
    $sz11 = $sz11 ?? '11px';
    $sz12 = $sz12 ?? '12px';

    $cData = is_array($r->culture_data) ? $r->culture_data : [];
    $growthStatus = $cData['growth_status'] ?? 'Growth';
    $isNoGrowth = ($growthStatus === 'No Growth');
    $isContamination = ($growthStatus === 'Contamination');

    $filledAntibiotics = collect($cData['antibiotics'] ?? [])->filter(function ($ab) {
        return !empty($ab['sensitivity']) || !empty($ab['mic']);
    })->values();

    $totalAb = $filledAntibiotics->count();
    $half = (int) ceil($totalAb / 2);
    $leftAbs = $filledAntibiotics->slice(0, $half);
    $rightAbs = $filledAntibiotics->slice($half);
@endphp

<div class="culture-block-container" style="font-family: {{ $fontFamily }}; width: 100%; margin: 4px 0 10px 0; page-break-inside: auto; clear: both;">
    {{-- ── Sub-heading if parameter name adds context ── --}}
    @if(!empty($r->parameter_name) && strtoupper(trim($r->parameter_name)) !== strtoupper(trim($testName ?? '')))
        <div style="font-weight: bold; font-size: {{ $sz10_5 }}; color: #0f172a; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
            {{ $r->parameter_name }}
        </div>
    @endif

    {{-- ── CULTURE FINDINGS SUMMARY BOX (Left-Right Grid) ── --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: {{ $sz9_5 }}; border: 1.5px solid #0f172a; background: #f8fafc;">
        <tr>
            <td style="padding: 4px 8px; width: 18%; font-weight: bold; color: #475569; border-bottom: 1px solid #cbd5e1;">
                Growth Status:
            </td>
            <td style="padding: 4px 8px; width: 32%; font-weight: bold; border-bottom: 1px solid #cbd5e1; border-right: 1px solid #cbd5e1;">
                @if($isNoGrowth)
                    <span style="color: #166534;">No Growth Isolated</span>
                @elseif($isContamination)
                    <span style="color: #991b1b;">Mixed Growth (Contamination)</span>
                @else
                    <span style="color: #1e40af;">Significant Growth Isolated</span>
                @endif
            </td>
            <td style="padding: 4px 8px; width: 18%; font-weight: bold; color: #475569; border-bottom: 1px solid #cbd5e1;">
                @if(!empty($labTest->sample_type))
                    Sample Type:
                @else
                    Colony Count:
                @endif
            </td>
            <td style="padding: 4px 8px; width: 32%; font-weight: bold; color: #0f172a; border-bottom: 1px solid #cbd5e1;">
                @if(!empty($labTest->sample_type))
                    {{ $labTest->sample_type }}
                @else
                    {{ $cData['colony_count'] ?? 'Not Specified' }}
                @endif
            </td>
        </tr>
        @if(!$isNoGrowth)
        <tr>
            <td style="padding: 4px 8px; font-weight: bold; color: #475569;">
                Organism Isolated:
            </td>
            <td style="padding: 4px 8px; font-weight: bold; font-style: italic; color: #0f172a; font-size: {{ $sz10 }}; border-right: 1px solid #cbd5e1;">
                {{ $cData['organism_name'] ?? 'Not Specified' }}
            </td>
            @if(!empty($labTest->sample_type))
                <td style="padding: 4px 8px; font-weight: bold; color: #475569;">
                    Colony Count:
                </td>
                <td style="padding: 4px 8px; font-weight: bold; color: #0f172a;">
                    {{ $cData['colony_count'] ?? 'Not Specified' }}
                </td>
            @else
                <td colspan="2" style="padding: 4px 8px;"></td>
            @endif
        </tr>
        @endif
    </table>

    {{-- ── ANTIBIOTIC SUSCEPTIBILITY PROFILE (Left Box & Right Box Layout) ── --}}
    @if(!$isNoGrowth && $totalAb > 0)
        <div style="margin-top: 6px;">
            <div style="font-weight: bold; font-size: {{ $sz10 }}; border-bottom: 1.5px solid #0f172a; padding-bottom: 3px; margin-bottom: 6px; text-transform: uppercase; color: #0f172a; letter-spacing: 0.5px;">
                Antibiotic Susceptibility Profile
                @if(!empty($cData['organism_name']))
                    <span style="font-size: {{ $sz9 }}; font-style: italic; font-weight: normal; color: #334155; text-transform: none; margin-left: 8px;">
                        (Organism: <strong>{{ $cData['organism_name'] }}</strong>)
                    </span>
                @endif
            </div>

            <table style="width: 100%; border-collapse: collapse; margin: 0; padding: 0;">
                <tr>
                    {{-- ══════════════ LEFT BOX ══════════════ --}}
                    <td style="width: 49%; vertical-align: top; padding: 0;">
                        <table class="antibiotic-box-table" style="width: 100%; border-collapse: collapse; border: 1.5px solid #0f172a; font-size: {{ $sz8_5 }};">
                            <thead>
                                <tr style="background: #e2e8f0; border-bottom: 1.5px solid #0f172a; color: #0f172a;">
                                    <th style="padding: 3px 6px; text-align: left; width: 48%; border-right: 1px solid #cbd5e1; font-weight: bold; text-transform: uppercase;">Antibiotic Name</th>
                                    <th style="padding: 3px 4px; text-align: center; width: 28%; border-right: 1px solid #cbd5e1; font-weight: bold; text-transform: uppercase;">Susceptibility</th>
                                    <th style="padding: 3px 4px; text-align: center; width: 24%; font-weight: bold; text-transform: uppercase;">MIC Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leftAbs as $ab)
                                    @php
                                        $sens = strtoupper(trim($ab['sensitivity'] ?? 'S'));
                                        $text = 'Sensitive';
                                        $color = '#15803d'; // Rich clinical green
                                        if ($sens === 'R') {
                                            $text = 'Resistant';
                                            $color = '#b91c1c'; // Clinical red
                                        } elseif ($sens === 'I') {
                                            $text = 'Intermediate';
                                            $color = '#b45309'; // Clinical amber
                                        }
                                        $bg = $loop->even ? '#f8fafc' : '#ffffff';
                                    @endphp
                                    <tr style="background: {{ $bg }}; border-bottom: 0.5px solid #e2e8f0; line-height: 1.25;">
                                        <td style="padding: 2px 6px; font-weight: 600; color: #0f172a; border-right: 1px solid #e2e8f0; white-space: nowrap; overflow: hidden;">
                                            {{ $ab['name'] }}
                                        </td>
                                        <td style="padding: 2px 4px; text-align: center; font-weight: bold; color: {{ $color }}; border-right: 1px solid #e2e8f0;">
                                            {{ $text }}
                                        </td>
                                        <td style="padding: 2px 4px; text-align: center; font-family: monospace; color: #334155;">
                                            {{ $ab['mic'] ?: '--' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>

                    {{-- ── GAP BETWEEN LEFT & RIGHT BOXES ── --}}
                    <td style="width: 2%; padding: 0;"></td>

                    {{-- ══════════════ RIGHT BOX ══════════════ --}}
                    <td style="width: 49%; vertical-align: top; padding: 0;">
                        <table class="antibiotic-box-table" style="width: 100%; border-collapse: collapse; border: 1.5px solid #0f172a; font-size: {{ $sz8_5 }};">
                            <thead>
                                <tr style="background: #e2e8f0; border-bottom: 1.5px solid #0f172a; color: #0f172a;">
                                    <th style="padding: 3px 6px; text-align: left; width: 48%; border-right: 1px solid #cbd5e1; font-weight: bold; text-transform: uppercase;">Antibiotic Name</th>
                                    <th style="padding: 3px 4px; text-align: center; width: 28%; border-right: 1px solid #cbd5e1; font-weight: bold; text-transform: uppercase;">Susceptibility</th>
                                    <th style="padding: 3px 4px; text-align: center; width: 24%; font-weight: bold; text-transform: uppercase;">MIC Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rightAbs as $ab)
                                    @php
                                        $sens = strtoupper(trim($ab['sensitivity'] ?? 'S'));
                                        $text = 'Sensitive';
                                        $color = '#15803d';
                                        if ($sens === 'R') {
                                            $text = 'Resistant';
                                            $color = '#b91c1c';
                                        } elseif ($sens === 'I') {
                                            $text = 'Intermediate';
                                            $color = '#b45309';
                                        }
                                        $bg = $loop->even ? '#f8fafc' : '#ffffff';
                                    @endphp
                                    <tr style="background: {{ $bg }}; border-bottom: 0.5px solid #e2e8f0; line-height: 1.25;">
                                        <td style="padding: 2px 6px; font-weight: 600; color: #0f172a; border-right: 1px solid #e2e8f0; white-space: nowrap; overflow: hidden;">
                                            {{ $ab['name'] }}
                                        </td>
                                        <td style="padding: 2px 4px; text-align: center; font-weight: bold; color: {{ $color }}; border-right: 1px solid #e2e8f0;">
                                            {{ $text }}
                                        </td>
                                        <td style="padding: 2px 4px; text-align: center; font-family: monospace; color: #334155;">
                                            {{ $ab['mic'] ?: '--' }}
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- If odd count, insert dummy empty row in right box to balance heights --}}
                                @if($rightAbs->count() < $leftAbs->count())
                                    @php
                                        $bg = ($rightAbs->count() + 1) % 2 === 0 ? '#f8fafc' : '#ffffff';
                                    @endphp
                                    <tr style="background: {{ $bg }}; border-bottom: none; line-height: 1.25;">
                                        <td style="padding: 2px 6px; border-right: 1px solid #e2e8f0;">&nbsp;</td>
                                        <td style="padding: 2px 4px; border-right: 1px solid #e2e8f0;">&nbsp;</td>
                                        <td style="padding: 2px 4px;">&nbsp;</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- ── Footnote / Legend ── --}}
            <div style="font-size: {{ $sz8 }}; color: #64748b; margin-top: 4px; font-style: italic;">
                <strong>Interpretation:</strong> S = Sensitive | I = Intermediate | R = Resistant | MIC = Minimum Inhibitory Concentration (µg/mL)
            </div>
        </div>
    @elseif($isNoGrowth)
        <div style="padding: 8px 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 4px; color: #166534; font-size: {{ $sz9 }}; margin-top: 6px;">
            <strong>Note:</strong> No pathogenic bacterial growth isolated after 48 hours of aerobic incubation at 37°C. Antibiotic susceptibility testing is not indicated.
        </div>
    @elseif($isContamination)
        <div style="padding: 8px 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 4px; color: #991b1b; font-size: {{ $sz9 }}; margin-top: 6px;">
            <strong>Note:</strong> Mixed bacterial flora isolated suggesting sample contamination. Repeat clean-catch midstream sample recommended.
        </div>
    @endif
</div>
