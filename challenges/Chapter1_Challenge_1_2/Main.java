public class Main {
    public static void main(String[] args) {
        // Array of winning lottery numbers as strings
        String[] winningNumbers = {
            "12-34-56-78-90",
            "33-44-11-66-22",
            "01-02-03-04-05"
        };

        // Variables to track the highest average and best ticket
        double highestAvg = 0;
        String bestTicket = "";

        // For-each loop to process each ticket
        for (String ticket : winningNumbers) {
            // Remove dashes from the ticket string
            String clean = ticket.replace("-", "");

            // Initialize sum
            int sum = 0;

            // For loop to iterate over each character in the cleaned string
            for (char c : clean.toCharArray()) {
                // Convert character to numeric value and add to sum
                sum += Character.getNumericValue(c);
            }

            // Calculate average
            double avg = (double) sum / clean.length();

            // Print analysis for this ticket
            System.out.println("Analyzing: " + ticket);
            System.out.println("Digit Sum: " + sum + ", Digit Average: " + avg);

            // Check if this average is the highest
            if (avg > highestAvg) {
                highestAvg = avg;
                bestTicket = ticket;
            }
        }

        // Print the ticket with the highest average
        System.out.println("\nThe winning number with the highest average is: "
                + bestTicket + " with an average of " + highestAvg);
    }
}