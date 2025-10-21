using MySql.Data.MySqlClient;
using Mysqlx.Crud;
using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;

namespace WpfRoosterMaker
{
    /// <summary>
    /// Interaction logic for MainWindow.xaml
    /// </summary>
    public partial class MainWindow : Window
    {
        public MainWindow()
        {
            InitializeComponent();
            LoadAgenda();
        }

        public static DataTable dt_klassen = new DataTable();
        public static DataTable dt_rooster = new DataTable();
        

        public static MySqlConnection Connect()
        {
            string connectionstring = "SERVER=localhost;DATABASE=roosterprojecttest;UID=root;PASSWORD=";
            try
            {
                MySqlConnection cnct = new MySqlConnection(connectionstring);
                return cnct;
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.ToString());
                return null;
            }

        }


        private void btnBackward_Click(object sender, RoutedEventArgs e)
        {
            ChangeDate(-1);
        }

        private void btnForward_Click(object sender, RoutedEventArgs e)
        {
            ChangeDate(1);
        }

        private void LoadAgenda()
        {
            dpWeek.SelectedDate = DateTime.Now;
            Read();
        }

        private void ChangeDate(int index)
        {
            dpWeek.SelectedDate = ((DateTime)dpWeek.SelectedDate).AddDays(index * 7);
        }

        private void dpWeek_SelectedDateChanged(object sender, SelectionChangedEventArgs e)
        {
            var newDate = (DateTime)e.AddedItems[0];

            while (newDate.DayOfWeek != dpWeek.FirstDayOfWeek)
                newDate = newDate.AddDays(-1);

            dpWeek.SelectedDate = newDate;
            Read();
        }

        private void btnCreate_Click(object sender, RoutedEventArgs e)
        {
            CreateWindow createwindow = new CreateWindow();
            createwindow.Owner = this;
            createwindow.ShowDialog();
        }

        public static void Create(string lesnaam, string klas, string datum, string fromtime, string totime)
        {
            MySqlConnection connection = Connect();
            MySqlCommand cmd = new MySqlCommand(@"INSERT INTO rooster (les, klas, datum, begintijd, eindtijd) VALUES (@les, @klas, @datum, @begintijd, @eindtijd)", connection);
            cmd.Parameters.AddWithValue("@les", lesnaam);
            cmd.Parameters.AddWithValue("@klas", klas);
            cmd.Parameters.AddWithValue("@datum", datum);
            cmd.Parameters.AddWithValue("@begintijd", fromtime);
            cmd.Parameters.AddWithValue("@eindtijd", totime);
            connection.Open();
            cmd.ExecuteNonQuery();
            connection.Close();
        }

        private void Read()
        {
            List list = new List();
            list.DataContext = gridAgenda.Children.OfType<List>;
            MySqlConnection connection = Connect();
            MySqlCommand cmd = new MySqlCommand("SELECT * FROM `rooster` WHERE `datum`=@datum", connection);
            connection.Open();
            dt_rooster.Load(cmd.ExecuteReader());
            connection.Close();
            cmd = new MySqlCommand("SELECT `klas` FROM `klassen`", connection);
            connection.Open();
            dt_klassen.Load(cmd.ExecuteReader());
            connection.Close();
        }

    }
}
