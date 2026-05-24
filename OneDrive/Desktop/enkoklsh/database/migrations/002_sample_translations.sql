-- Sample translations for testing Amharic (am) and Afaan Oromo (om)
-- Update question IDs as needed if your DB IDs differ.

-- Question ID 1
UPDATE questions SET
  question_am = 'የኢትዮጵያ መጨረሻ ንጉሥ ማን ነው?',
  question_om = 'Mootii dhumaa Itoophiyaa eenyu ture?',
  option_a_am = 'ኃይለ ሥላሴ',
  option_a_om = 'Haayilee Silaasee',
  option_b_am = 'መነልክ ፪',
  option_b_om = 'Menelik II',
  option_c_am = 'ዮሃንስ ፬',
  option_c_om = 'Yohaannis IV',
  option_d_am = 'ተድሮስ ፪',
  option_d_om = 'Tewodros II'
WHERE id = 1;

-- Question ID 2
UPDATE questions SET
  question_am = 'የአድዋ ጦርነት በትኛው ዓመት ነበር?',
  question_om = 'Waggaa kamiitti lolaan Adwaa geggeeffame?',
  option_a_am = '1885', option_a_om = '1885',
  option_b_am = '1896', option_b_om = '1896',
  option_c_am = '1905', option_c_om = '1905',
  option_d_am = '1911', option_d_om = '1911'
WHERE id = 2;

-- Question ID 3
UPDATE questions SET
  question_am = 'የድንበር የድሮ መንግስት የተመሰረበው በየት ነው?',
  question_om = 'Mootummaa durii kamtu kaaba Itoophiyaa keessa ture?',
  option_a_am = 'ኩሽ', option_a_om = 'Kush',
  option_b_am = 'አክሱም', option_b_om = 'Axum',
  option_c_am = 'ሜሮዬ', option_c_om = 'Meroe',
  option_d_am = 'ሸባ', option_d_om = 'Sheba'
WHERE id = 3;

-- Question ID 6
UPDATE questions SET
  question_am = 'አዲስ አበባ የመነሻዋ ስም ምን ነው?',
  question_om = 'Maqaan jalqabaa Adaamaa maal ture? (Addis Ababa)',
  option_a_am = 'ፊንፊን', option_a_om = 'Finfinne',
  option_b_am = 'ጎንዳር', option_b_om = 'Gondar',
  option_c_am = 'ኣክሱም', option_c_om = 'Axum',
  option_d_am = 'ላሊበላ', option_d_om = 'Lalibela'
WHERE id = 6;

-- After running this file, refresh the quiz page and choose the language
-- from the navbar to verify translations appear. If your question IDs
-- differ, adjust the WHERE id = X clauses accordingly.
