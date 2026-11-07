/**
 * The Christian year, computed — mirrors App\Services\LiturgicalCalendarService.
 * A liturgical year runs from the First Sunday of Advent to the Saturday
 * before the next one.
 */

const DAY = 86400000;

const startOfDay = (d) => new Date(d.getFullYear(), d.getMonth(), d.getDate());
const addDays = (d, n) => new Date(d.getFullYear(), d.getMonth(), d.getDate() + n);

export function easterSunday(year) {
    const a = year % 19;
    const b = Math.floor(year / 100);
    const c = year % 100;
    const d = Math.floor(b / 4);
    const e = b % 4;
    const f = Math.floor((b + 8) / 25);
    const g = Math.floor((b - f + 1) / 3);
    const h = (19 * a + b - d - g + 15) % 30;
    const i = Math.floor(c / 4);
    const k = c % 4;
    const l = (32 + 2 * e + 2 * i - h - k) % 7;
    const m = Math.floor((a + 11 * h + 22 * l) / 451);
    const month = Math.floor((h + l - 7 * m + 114) / 31);
    const day = ((h + l - 7 * m + 114) % 31) + 1;
    return new Date(year, month - 1, day);
}

export function firstSundayOfAdvent(year) {
    const christmas = new Date(year, 11, 25);
    const dow = christmas.getDay();
    return addDays(christmas, -(dow === 0 ? 28 : 21 + dow));
}

/** Seasons in liturgical colour. `ink` is the colour on dark surfaces, `paper` on light. */
export const SEASON_STYLE = {
    advent: { name: 'Advent', ink: '#8B6BC9', paper: '#6B3FA0', blurb: 'Expectation & hope' },
    christmas: { name: 'Christmas', ink: '#E8C97A', paper: '#C9A24A', blurb: 'The Word made flesh' },
    epiphany: { name: 'Epiphany', ink: '#4FA36E', paper: '#2F7A4B', blurb: 'Light to the nations' },
    lent: { name: 'Lent', ink: '#7B4FB0', paper: '#4B2470', blurb: 'Forty days of renewal' },
    holyweek: { name: 'Holy Week', ink: '#C4384B', paper: '#8B1E2F', blurb: 'The way of the cross' },
    easter: { name: 'Easter', ink: '#F1D58F', paper: '#C9A24A', blurb: 'Christ is risen' },
    pentecost: { name: 'Pentecost', ink: '#FF4D5E', paper: '#C8102E', blurb: 'Tongues of fire' },
    kingdomtide: { name: 'After Pentecost', ink: '#4FA36E', paper: '#2F7A4B', blurb: 'Growing in grace' },
};

/** Segments of the liturgical year that contains `today`. */
export function liturgicalYear(today = new Date()) {
    today = startOfDay(today);
    let startYear = today.getFullYear();
    if (today < firstSundayOfAdvent(startYear)) startYear -= 1;

    const start = firstSundayOfAdvent(startYear);
    const end = addDays(firstSundayOfAdvent(startYear + 1), -1);
    const y = startYear + 1;
    const easter = easterSunday(y);
    const ash = addDays(easter, -46);
    const palm = addDays(easter, -7);
    const pentecost = addDays(easter, 49);

    const raw = [
        ['advent', start, new Date(startYear, 11, 24)],
        ['christmas', new Date(startYear, 11, 25), new Date(y, 0, 5)],
        ['epiphany', new Date(y, 0, 6), addDays(ash, -1)],
        ['lent', ash, addDays(palm, -1)],
        ['holyweek', palm, addDays(easter, -1)],
        ['easter', easter, addDays(pentecost, -1)],
        ['pentecost', pentecost, pentecost],
        ['kingdomtide', addDays(pentecost, 1), end],
    ];

    const total = Math.round((end - start) / DAY) + 1;
    const segments = raw.map(([key, s, e]) => ({
        key,
        ...SEASON_STYLE[key],
        start: s,
        end: e,
        from: Math.round((s - start) / DAY) / total,
        to: (Math.round((e - start) / DAY) + 1) / total,
        days: Math.round((e - s) / DAY) + 1,
    }));

    const dayIndex = Math.round((today - start) / DAY);
    const current = segments.find((s) => today >= s.start && today <= s.end) || segments[0];
    const next = segments[(segments.indexOf(current) + 1) % segments.length];

    return {
        start,
        end,
        total,
        today,
        position: (dayIndex + 0.5) / total,
        segments,
        current,
        next,
        weekOfSeason: Math.floor((today - current.start) / DAY / 7) + 1,
        weeksInSeason: Math.ceil(current.days / 7),
        daysToNext: Math.round((addDays(current.end, 1) - today) / DAY),
        // Months that begin inside this liturgical year, for the dial's outer scale
        months: Array.from({ length: 13 }, (_, i) => new Date(start.getFullYear(), start.getMonth() + i, 1))
            .filter((m) => m > start && m <= end)
            .map((m) => ({ date: m, at: Math.round((m - start) / DAY) / total })),
        sundays: Array.from({ length: Math.ceil(total / 7) }, (_, i) => (i * 7) / total),
    };
}

export const fmt = (d, opts = { day: 'numeric', month: 'short' }) => d.toLocaleDateString('en-GB', opts);
