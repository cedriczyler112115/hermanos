@extends('layouts.site')

@section('title', 'Choir History · Cantores Hermanos')

@section('content')
    <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10">
        <div class="max-w-3xl">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">About the Cantores Hermanos</h1>

        </div>

        <div class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-3xl bg-[var(--color-muted)] p-6 ring-1 ring-[var(--color-border)] lg:col-span-2">
                <h2 class="text-lg font-semibold text-slate-900">Purpose</h2>
                <div class="mt-2 space-y-4 text-sm leading-relaxed text-slate-700">
                    <p>
                        The purpose of this community is to serve God through music by leading the congregation in song during Mass, and to contribute to the community through special projects that promote its advocacies and goodwill.
                    </p>
                </div>

                <h2 class="mt-6 text-lg font-semibold text-slate-900">Objectives</h2>
                <div class="mt-2 space-y-3 text-sm leading-relaxed text-slate-700">
                    <p>
                        We foster a deeper understanding of the Word of God through the gift of song, and we engage members in activities that nurture spiritual growth.
                    </p>
                    <p>
                        We raise the musical standard among the group’s members by strengthening music theory, vocal technique, and liturgical formation, while also creating and releasing original religious compositions.
                    </p>
                    <p>
                        We promote musical appreciation among members and the public, and we carry out projects that serve the community in meaningful ways—serving both God and the community as a whole through music.
                    </p>
                </div>

                <h2 class="mt-6 text-lg font-semibold text-slate-900">Outreach</h2>
                <div class="mt-2 space-y-4 text-sm leading-relaxed text-slate-700">
                    <p>
                        We promote the name and mission of CHDSSNC beyond the local community by engaging in outreach programs that support religious, educational, development, and cultural causes.
                    </p>
                </div>

                <h2 class="mt-6 text-lg font-semibold text-slate-900">Stewardship</h2>
                <div class="mt-2 space-y-4 text-sm leading-relaxed text-slate-700">
                    <p>
                        The CHDSSNC actively participates in environmentally conscious activities by organizing “green” initiatives, reducing waste, and encouraging sustainability in all choir activities.
                    </p>
                </div>

                <h2 class="mt-6 text-lg font-semibold text-slate-900">Eligibility & Membership</h2>
                <div class="mt-2 space-y-4 text-sm leading-relaxed text-slate-700">
                    <p>
                        CHDSSNC is open to all Roman Catholic male individuals who are at least eighteen (18) years old.
                    </p>
                    <p>
                        Aspiring members undergo a six-month probationary period. During this time, they attend Mass schedules every Friday at 6:00 PM and Sunday at 10:00 AM, and they participate in choir services, parish-related rehearsals, celebrations or events, meetings, fellowships, and training sessions.
                    </p>
                    <p>
                        Aspiring members demonstrate willingness and commitment to serve God through active participation in the activities of CHDSSNC. After successfully completing the six-month probationary period, members are granted a bullring, provided they cover the cost of the item.
                    </p>
                    <p>
                        In cases of unseemly conduct, the member will be subject to group deliberation. Disciplinary actions, including suspension or termination of membership, may be enforced by a majority vote of the members for the common good of the organization.
                    </p>
                    <p>
                        Members who work abroad or study away from home, but who continue to support the organization’s endeavors, are exempt from the attendance requirements during their time away.
                    </p>
                </div>

                <h2 class="mt-6 text-lg font-semibold text-slate-900">Member Privileges</h2>
                <div class="mt-2 space-y-3 text-sm leading-relaxed text-slate-700">
                    <p>
                        Active members are supported in spiritual growth through regular prayer sessions, reflections, recollections, and retreats.
                    </p>
                    <p>
                        Members gain access to training and education through workshops in music theory, liturgical music, vocal technique, and performance skills. Uniforms or choir attire may also be provided, subject to availability of funds.
                    </p>
                    <p>
                        Members are recognized for their service and contributions during special events and anniversary celebrations.
                    </p>
                </div>

                <h2 class="mt-6 text-lg font-semibold text-slate-900">Spiritual Accountability</h2>
                <div class="mt-2 space-y-4 text-sm leading-relaxed text-slate-700">
                    <p>
                        Members are expected to attend rehearsals, Masses, meetings, and other events regularly. They actively promote the group’s values and objectives, encourage new members, and participate in group activities.
                    </p>
                    <p>
                        Members treat musical instruments, uniforms, and other resources with care. They adhere to the brotherhood, spiritual life, and disciplines of the Choir, and they participate in significant parish-related celebrations, community outreach, or fundraising activities.
                    </p>
                </div>

                <h2 class="mt-6 text-lg font-semibold text-slate-900">Fellowship Rights</h2>
                <div class="mt-2 space-y-4 text-sm leading-relaxed text-slate-700">
                    <p>
                        Members have voting rights in elections of officers and may be nominated for leadership positions within the Choir.
                    </p>
                </div>

                <h2 class="mt-6 text-lg font-semibold text-slate-900">Membership Changes</h2>
                <div class="mt-2 space-y-4 text-sm leading-relaxed text-slate-700">
                    <p>
                        A member may voluntarily withdraw from the Choir with valid reasons by submitting a written resignation. Membership may also be removed for failure to follow the rules, duties, responsibilities, or disciplines of the Choir.
                    </p>
                    <p>
                        A “sacred pause” is a temporary break from choir duties due to valid reasons such as illness, work, studies, or personal matters. Members should notify the Officers or any Board members with the reason and expected return date, except in emergencies. During leave, members are not permitted to join another choir or take an active role, except in special case-by-case situations. Members should notify the Choir Officers before returning for restoration.
                    </p>
                </div>
            </div>

<div class="rounded-3xl border border-[var(--color-border)] bg-white p-6 shadow-sm">

    <!-- Title -->
    <h2 class="text-lg font-semibold text-slate-900 text-center">
        About the Logo
    </h2>

    <!-- Image -->
    <div class="mt-6 flex justify-center">
        <img 
            src="{{ file_exists(storage_path('app/public/logo/logo.jpg')) 
                ? asset('storage/logo/logo.jpg') 
                : asset('favicon.ico') }}" 
            alt="Cantores Hermanos choir logo"
            class="h-32 w-32 rounded-full object-cover shadow-md ring-2 ring-slate-100"
        />
    </div>

    <!-- Content -->
    <div class="mt-6 space-y-4 text-sm text-slate-700 leading-relaxed">

        <p>
            The name <span class="font-medium text-slate-900">Cantores Hermanos del Sr. Sto. Niño</span>
            is prominently displayed in the overall design of the logo.
        </p>

        <p>
            The word <span class="font-medium text-slate-900">“Cantores”</span> is uniquely styled to resemble a guitar,
            symbolizing the organization’s foundation through music. The guitar represents creativity,
            freedom, and individual expression.
        </p>

        <p>
            The guitar’s sound hole and bridge form the number <span class="font-semibold">10</span>,
            representing the ten founding members:
        </p>

        <!-- Founders List -->
        <ol class="list-decimal space-y-1 pl-6 text-slate-600">
            <li>Rev. Fr. Juanito Belino</li>
            <li>Peter A. Peteros</li>
            <li>Victor Cubillas</li>
            <li>Kenneth Michael R. Feliciano</li>
            <li>Warren Bunglay</li>
            <li>Vincent C. Rosario</li>
            <li>Victamer C. Rosario</li>
            <li>Voltaire Anthony C. Rosario</li>
            <li>Ronaldo T. Bedrijo</li>
            <li>Emerson Rabaya</li>
        </ol>

        <p>
            The Cross embedded in the letter <span class="font-medium">“H”</span> of “Hermanos”
            represents sacrifice and faith, placing Jesus Christ at the center of the group and its members’ lives.
        </p>

        <p>
            The year <span class="font-semibold">1999</span> commemorates the founding of the group on
            February 14, 1999, with the blessing of Rev. Fr. Juanito Belino, who coined the name
            <em>Cantores Hermanos del Sr. Sto. Niño</em>.
        </p>

    </div>
</div>
        </div>
    </div>
@endsection
