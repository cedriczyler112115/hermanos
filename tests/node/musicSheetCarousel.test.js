import test from 'node:test';
import assert from 'node:assert/strict';
import { musicSheetCarouselBoundedNextIndex } from '../../resources/js/app.js';

test('unit: musicSheetCarouselBoundedNextIndex returns -1 at boundaries', () => {
    assert.equal(musicSheetCarouselBoundedNextIndex(0, -1, 5), -1);
    assert.equal(musicSheetCarouselBoundedNextIndex(4, 1, 5), -1);
});

test('unit: musicSheetCarouselBoundedNextIndex moves left/right within bounds', () => {
    assert.equal(musicSheetCarouselBoundedNextIndex(2, -1, 5), 1);
    assert.equal(musicSheetCarouselBoundedNextIndex(2, 1, 5), 3);
});

test('unit: musicSheetCarouselBoundedNextIndex handles invalid inputs', () => {
    assert.equal(musicSheetCarouselBoundedNextIndex(NaN, 1, 5), 1);
    assert.equal(musicSheetCarouselBoundedNextIndex(0, 1, 0), -1);
});

