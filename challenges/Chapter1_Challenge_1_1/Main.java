public class Main {
    public static void main(String[] args) {
        int number = 13579;

        int digits = (int) Math.log10(number) + 1;
        int firstDigit = number / (int) Math.pow(10, digits - 1);
        int lastDigit = number % 10;

        int secondDigit = (number / (int) Math.pow(10, digits - 2)) % 10;
        int secondLastDigit = (number / 10) % 10;

        int product = firstDigit * lastDigit;
        int sum = secondDigit + secondLastDigit;

        String finalCode = "" + product + sum;
        System.out.println("The decrypted code is: " + finalCode);
    }
}