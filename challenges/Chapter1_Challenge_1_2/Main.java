public class Main {
    public static void main(String[] args) {
        String[] winningNumbers = {
            "12-34-56-78-90",
            "33-44-11-66-22",
            "01-02-03-04-05"
        };

        double highestAvg = 0;
        String bestTicket = "";

        for (String ticket : winningNumbers) {
            String clean = ticket.replace("-", "");
            int sum = 0;

            for (char c : clean.toCharArray()) {
                sum += Character.getNumericValue(c);
            }

            double avg = (double) sum / clean.length();
            System.out.println("Analyzing: " + ticket);
            System.out.println("Digit Sum: " + sum + ", Digit Average: " + avg);

            if (avg > highestAvg) {
                highestAvg = avg;
                bestTicket = ticket;
            }
        }

        System.out.println("\nThe winning number with the highest average is: "
                + bestTicket + " with an average of " + highestAvg);
    }
}