public class Main {
    public static void main(String[] args) {
        // Example input: positive integer
        int number = 13579;

        // Calculate the number of digits
        int digits = (int) Math.log10(number) + 1;

        // Extract first digit
        int firstDigit = number / (int) Math.pow(10, digits - 1);

        // Extract last digit
        int lastDigit = number % 10;

        // Extract second digit
        int secondDigit = (number / (int) Math.pow(10, digits - 2)) % 10;

        // Extract second-last digit
        int secondLastDigit = (number / 10) % 10;

        // Calculate product of first and last digits
        int product = firstDigit * lastDigit;

        // Calculate sum of second and second-last digits
        int sum = secondDigit + secondLastDigit;

        // Concatenate product and sum as string
        String finalCode = "" + product + sum;

        // Output the decrypted code
        System.out.println("The decrypted code is: " + finalCode);
    }
}